<?php

namespace App\Controllers\Api;

use App\Models\NotificationModel;
use App\Models\OrderRefundModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\OrderStatusModel;
use App\Models\UserRefundAccountModel;
use Config\OrderStatus;

class RefundApiController extends BaseApiController
{
  protected $refundModel;
  protected $orderModel;
  protected $orderItemModel;
  protected $userRefundAccountModel;
  protected $statusModel;
  protected $notificationModel;

  public function __construct()
  {
    $this->refundModel = new OrderRefundModel();
    $this->orderModel = new OrderModel();
    $this->orderItemModel = new OrderItemModel();
    $this->userRefundAccountModel = new UserRefundAccountModel();
    $this->statusModel = new OrderStatusModel();
    $this->notificationModel = new NotificationModel();
  }

  public function checkCancelStatus(string $orderId)
  {
    // Auth
    $customerId = $this->getAuthenticatedCustomerId();

    // Validate order
    if (strlen($orderId) !== 36) {
      return $this->errorResponse('Invalid order ID');
    }

    // Ambil order + ownership
    $order = $this->orderModel
      ->where('order_id', $orderId)
      ->where('customer_id', $customerId)
      ->first();

    if (!$order) {
      return $this->errorResponse('Order not found');
    }

    // Cari cancel request
    $refund = $this->refundModel
      ->select('order_refund_id, status, refund_amount, created_at')
      ->where('order_id', $orderId)
      ->where('type', OrderRefundModel::TYPE_CANCEL)
      ->orderBy('created_at', 'DESC')
      ->first();

    // === BELUM PERNAH REQUEST CANCEL ===
    if (!$refund) {
      return $this->successResponse([
        'order_id' => $orderId,
        'has_cancel_request' => false,
        'cancel_status' => null,
      ], 'No cancellation request found');
    }

    // === SUDAH REQUEST CANCEL ===
    return $this->successResponse([
      'order_id' => $orderId,
      'has_cancel_request' => true,
      'cancel_status' => $refund['status'], // pending / approved / rejected
      'refund_amount' => $refund['refund_amount'],
      'requested_at' => $refund['created_at'],
    ], 'Cancellation request found');
  }


  // =====================================================
  // CANCEL ORDER API
  // =====================================================
  public function submitCancel()
  {
    // Check ownership
    $customerId = $this->getAuthenticatedCustomerId();
    $customerName = $this->getAuthenticatedCustomerName();

    if (!$this->validate($this->refundModel->validationRules)) {
      return $this->validationErrorResponse($this->validator->getErrors(), 'Validation failed');
    }

    $orderId = $this->request->getJSON()->order_id;
    $reason = $this->request->getJSON()->reason;
    $additionalNote = $this->request->getJSON()->additional_note;

    $order = $this->orderModel->find($orderId);

    if (!$order) {
      return $this->errorResponse('Order not found');
    }

    $STATUS_PENDING = $this->statusModel->getIdByCode(OrderStatus::PENDING);
    $STATUS_CANCELLED = $this->statusModel->getIdByCode(OrderStatus::CANCELLED);

    // === CASE 1: Belum bayar - Cancel langsung tanpa refund ===
    if ($order['status_id'] === $STATUS_PENDING) {
      $this->orderModel->update($orderId, [
        'status_id' => $STATUS_CANCELLED,
      ]);

      return $this->messageResponse('Order has been cancelled successfully');
    } else {
      // === CASE 2: Sudah bayar - Perlu proses refund ===
      $refundAccount = $this->userRefundAccountModel
        ->select('user_refund_account_id')
        ->where('customer_id', $customerId)
        ->first();
      log_message('debug', $refundAccount['user_refund_account_id']);

      if (!$refundAccount) {
        return $this->errorResponse(
          'User refund account is required because the order has already been paid'
        );
      }

      $data = [
        'order_id' => $orderId,
        'type' => OrderRefundModel::TYPE_CANCEL,
        'user_refund_account_id' => $refundAccount['user_refund_account_id'],
        'refund_amount' => $order['grand_total'],
        'reason' => $reason,
        'additional_note' => $additionalNote,
        'status' => 'pending'
      ];

      $result = $this->refundModel->insert($data);
      if (!$result) {
        return $this->errorResponse($result['errors'] ?? null,  $result['message']);
      }
      $refundId = $this->refundModel->getInsertID();

      // Kirim notifikasi ke admin untuk review
      $this->notificationModel->addNotification('cancel_order', "New cancellation request from {$customerName}", $refundId);

      $response = [
        'order_id' => $orderId,
        'refund_id' => $refundId,
        'status' => 'cancellation_requested',
        'refund_status' => 'pending',
        'refund_amount' => $order['grand_total'],
        'auto_approved' => false,
      ];
      return $this->successResponse($response, 'Cancellation request submitted successfully. Our admin will review it shortly.');
    }
  }

  // =====================================================
  // REFUND ORDER API
  // =====================================================

  /**
   * Submit refund request
   * POST /api/refund
   * 
   * Request Body:
   * {
   *   "order_id": "xxx-xxx-xxx",
   *   "refund_type": "full" or "partial",
   *   "refund_amount": 150000,
   *   "selected_items": ["item-id-1", "item-id-2"], // for partial
   *   "reason": "Alasan refund",
   *   "user_refund_account_id": "yyy-yyy-yyy"
   * }
   */
  public function submitRefund()
  {
    $rules = [
      'order_id' => 'required|min_length[36]|max_length[36]',
      'refund_type' => 'required|in_list[full,partial]',
      'refund_amount' => 'required|decimal|greater_than[0]',
      'reason' => 'required|min_length[10]',
      'user_refund_account_id' => 'required',
    ];

    if (!$this->validate($rules)) {
      return $this->validationErrorResponse($this->validator->getErrors());
    }

    $json = $this->request->getJSON();
    $orderId = $json->order_id;
    $refundType = $json->refund_type;
    $refundAmount = $json->refund_amount;
    $selectedItems = $json->selected_items ?? [];

    $order = $this->orderModel->find($orderId);

    if (!$order) {
      return $this->notFoundResponse('Order not found');
    }

    // Check ownership
    $this->getAuthenticatedUser();

    // Check if order eligible for refund
    if (!$this->isEligibleForRefund($order)) {
      return $this->errorResponse('Order tidak eligible untuk refund');
    }

    // Validasi refund amount
    $maxRefundAmount = $this->calculateMaxRefundAmount($orderId, $refundType, $selectedItems);

    if ($refundAmount > $maxRefundAmount) {
      return $this->successResponse([
        'max_refund_amount' => $maxRefundAmount,
        'requested_amount' => $refundAmount,
      ], 'Jumlah refund melebihi maksimal yang diperbolehkan');
    }

    // Create refund request
    $data = [
      'order_id' => $orderId,
      'type' => OrderRefundModel::TYPE_REFUND,
      'user_refund_account_id' => $json->user_refund_account_id,
      'refund_amount' => $refundAmount,
      'reason' => $json->reason,
    ];

    $result = $this->refundModel->createRefund($data);

    if (!$result['success']) {
      return $this->errorResponse($result['message'], $result['errors'] ?? null);
    }

    // Update order status
    $this->orderModel->update($orderId, [
      'refund_status' => 'requested',
    ]);

    // Send notification to admin
    $this->sendNotificationToAdmin($result['refund_id']);

    $response = [
      'order_id' => $orderId,
      'refund_id' => $result['refund_id'],
      'refund_type' => $refundType,
      'refund_amount' => $refundAmount,
      'status' => 'pending',
    ];

    return $this->successResponse($response);
  }

    // =====================================================
    // ADMIN API
    // =====================================================

  /**
   * Get refund/cancel detail
   * GET /api/admin/refund/{refundId}
   */
  public function getRefundDetail($refundId)
  {
    $refund = $this->refundModel->withAll()->find($refundId);

    if (!$refund) {
      return $this->notFoundResponse('Refund not found');
    }

    return $this->successResponse($refund);
  }

  /**
   * Get all pending refunds/cancellations
   * GET /api/admin/refunds?type=cancel&status=pending
   */
  public function getPendingRefunds()
  {
    $type = $this->request->getGet('type'); // cancel or refund
    $status = $this->request->getGet('status') ?? 'pending';

    $builder = $this->refundModel->withAll();

    if ($type) {
      $builder->where('order_refunds.type', $type);
    }

    if ($status) {
      $builder->where('order_refunds.status', $status);
    }

    $refunds = $builder->orderBy('order_refunds.created_at', 'DESC')->findAll();

    $response = [
      'refunds' => $refunds,
      'total'   => count($refunds)
    ];
    return $this->successResponse($response);
  }

  /**
   * Admin approve refund/cancel
   * POST /api/admin/refund/{refundId}/approve
   * 
   * Request Body:
   * {
   *   "admin_note": "Optional note",
   *   "adjusted_amount": 150000 (optional)
   * }
   */
  public function adminApprove($refundId)
  {
    $adminId = $this->request->getHeaderLine('X-Admin-Id') ?? session('admin_id');
    $json = $this->request->getJSON();
    $adminNote = $json->admin_note ?? null;
    $adjustedAmount = $json->adjusted_amount ?? null;

    $refund = $this->refundModel->find($refundId);

    if (!$refund) {
      return $this->notFoundResponse('Refund not found');
    }

    // Jika admin adjust amount
    if ($adjustedAmount && $adjustedAmount != $refund['refund_amount']) {
      $this->refundModel->update($refundId, [
        'refund_amount' => $adjustedAmount,
      ]);
    }

    // Approve refund
    if (!$this->refundModel->markApproved($refundId, $adminId, $adminNote)) {
      return $this->errorResponse('Approve refund failed');
    }

    // Update order status based on type
    if ($refund['type'] === OrderRefundModel::TYPE_CANCEL) {
      $this->orderModel->update($refund['order_id'], [
        'status' => 'cancelled',
        'cancelled_at' => date('Y-m-d H:i:s'),
      ]);
    } else {
      $this->orderModel->update($refund['order_id'], [
        'refund_status' => 'approved',
      ]);
    }

    // Process actual refund
    $this->processRefundTransaction($refundId);

    // Send notification to customer
    $this->sendNotificationToCustomer($refundId, 'approved');

    $response = [
      'refund_id' => $refundId,
      'order_id' => $refund['order_id'],
      'status' => 'approved',
      'refund_amount' => $adjustedAmount ?? $refund['refund_amount'],
    ];

    return $this->successResponse($response, 'Refund approve successfully');
  }

  /**
   * Admin reject refund/cancel
   * POST /api/admin/refund/{refundId}/reject
   * 
   * Request Body:
   * {
   *   "admin_note": "Required reason for rejection"
   * }
   */
  public function adminReject($refundId)
  {
    $adminId = $this->request->getHeaderLine('X-Admin-Id') ?? session('admin_id');
    $json = $this->request->getJSON();
    $adminNote = $json->admin_note ?? null;

    if (empty($adminNote)) {
      return $this->errorResponse('Note is requires!');
    }

    $refund = $this->refundModel->find($refundId);

    if (!$refund) {
      return $this->notFoundResponse('Refund not found');
    }

    if (!$this->refundModel->markRejected($refundId, $adminId, $adminNote)) {
      return $this->errorResponse('reject refund failed');
    }

    // Update order status
    if ($refund['type'] === OrderRefundModel::TYPE_CANCEL) {
      $this->orderModel->update($refund['order_id'], [
        'status' => 'processing', // Kembalikan ke status sebelumnya
      ]);
    } else {
      $this->orderModel->update($refund['order_id'], [
        'refund_status' => 'rejected',
      ]);
    }

    // Send notification to customer
    $this->sendNotificationToCustomer($refundId, 'rejected');

    $response = [
      'refund_id' => $refundId,
      'order_id' => $refund['order_id'],
      'status' => 'rejected',
      'admin_note' => $adminNote,
    ];
    return $this->successResponse($response, 'Refund reject successfully');
  }

  // =====================================================
  // HELPER METHODS
  // =====================================================

  private function isEligibleForRefund($order): bool
  {
    if ($order['payment_status'] !== 'paid') {
      return false;
    }

    $eligibleStatuses = ['delivered', 'completed'];
    if (!in_array($order['status'], $eligibleStatuses)) {
      return false;
    }

    // Check refund period (7 days)
    if (!empty($order['delivered_at'])) {
      $deliveredDate = strtotime($order['delivered_at']);
      $currentDate = time();
      $daysDiff = ($currentDate - $deliveredDate) / (60 * 60 * 24);

      if ($daysDiff > 7) {
        return false;
      }
    }

    return true;
  }

  private function calculateMaxRefundAmount($orderId, $refundType, $selectedItems = [])
  {
    $order = $this->orderModel->find($orderId);

    if ($refundType === 'full') {
      return $order['total_amount'];
    }

    if (!empty($selectedItems)) {
      $items = $this->orderItemModel->whereIn('order_item_id', $selectedItems)->findAll();
      return array_sum(array_column($items, 'subtotal'));
    }

    return 0;
  }

  private function canAutoApproveCancellation($order): bool
  {
    $autoApproveStatuses = ['pending', 'confirmed'];
    return in_array($order['status'], $autoApproveStatuses);
  }

  private function autoApproveCancellation($refundId, $orderId)
  {
    $this->refundModel->markApproved($refundId, null, 'Auto approved - order belum diproses');
    $this->orderModel->update($orderId, [
      'status' => 'cancelled',
      'cancelled_at' => date('Y-m-d H:i:s'),
    ]);
    $this->processRefundTransaction($refundId);
    $this->sendNotificationToCustomer($refundId, 'approved');
  }

  private function processRefundTransaction($refundId)
  {
    log_message('info', "Processing refund transaction for refund_id: {$refundId}");
    return true;
  }

  private function sendNotificationToAdmin($refundId)
  {
    log_message('info', "Sending notification to admin for refund_id: {$refundId}");
  }

  private function sendNotificationToCustomer($refundId, $status)
  {
    log_message('info', "Sending {$status} notification to customer for refund_id: {$refundId}");
  }
}
