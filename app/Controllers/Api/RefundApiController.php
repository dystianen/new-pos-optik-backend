<?php

namespace App\Controllers\Api;

use App\Models\OrderRefundModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use CodeIgniter\HTTP\ResponseInterface;

class RefundApiController extends BaseApiController
{
  protected $refundModel;
  protected $orderModel;
  protected $orderItemModel;

  public function __construct()
  {
    $this->refundModel = new OrderRefundModel();
    $this->orderModel = new OrderModel();
    $this->orderItemModel = new OrderItemModel();
  }

    // =====================================================
    // CANCEL ORDER API
    // =====================================================

  /**
   * Submit cancel order request
   * POST /api/cancel
   * 
   * Request Body:
   * {
   *   "order_id": "xxx-xxx-xxx",
   *   "reason": "Alasan pembatalan",
   *   "user_refund_account_id": "yyy-yyy-yyy" (optional, required jika sudah bayar)
   * }
   */
  public function submitCancel()
  {
    $rules = [
      'order_id' => 'required|min_length[36]|max_length[36]',
      'reason' => 'required|min_length[10]',
    ];

    if (!$this->validate($rules)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $this->validator->getErrors(),
      ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
    }

    $orderId = $this->request->getJSON()->order_id;
    $reason = $this->request->getJSON()->reason;

    $order = $this->orderModel->find($orderId);

    if (!$order) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Order tidak ditemukan',
      ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
    }

    // Check ownership
    $userId = $this->request->getHeaderLine('X-User-Id') ?? session('user_id');
    if ($order['user_id'] !== $userId) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Unauthorized',
      ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
    }

    // Check if can be cancelled
    if (!$this->canBeCancelled($order)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Order tidak bisa dibatalkan karena sudah ' . $order['status'],
      ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
    }

    // Check if already have active cancel request
    if ($this->refundModel->hasActiveRefund($orderId)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Anda sudah mengajukan pembatalan untuk order ini',
      ])->setStatusCode(ResponseInterface::HTTP_CONFLICT);
    }

    // === CASE 1: Belum bayar - Cancel langsung tanpa refund ===
    if ($order['payment_status'] !== 'paid') {
      $this->orderModel->update($orderId, [
        'status' => 'cancelled',
        'cancel_reason' => $reason,
        'cancelled_at' => date('Y-m-d H:i:s'),
      ]);

      return $this->response->setJSON([
        'success' => true,
        'message' => 'Order berhasil dibatalkan',
        'data' => [
          'order_id' => $orderId,
          'status' => 'cancelled',
          'refund_needed' => false,
        ],
      ])->setStatusCode(ResponseInterface::HTTP_OK);
    }

    // === CASE 2: Sudah bayar - Perlu proses refund ===
    $refundAccountId = $this->request->getJSON()->user_refund_account_id ?? null;

    if (empty($refundAccountId)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'user_refund_account_id diperlukan karena order sudah dibayar',
      ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
    }

    $data = [
      'order_id' => $orderId,
      'type' => OrderRefundModel::TYPE_CANCEL,
      'user_refund_account_id' => $refundAccountId,
      'refund_amount' => $order['total_amount'], // Always full refund for cancellation
      'reason' => $reason,
    ];

    $result = $this->refundModel->createCancellation($data);

    if (!$result['success']) {
      return $this->response->setJSON([
        'success' => false,
        'message' => $result['message'],
        'errors' => $result['errors'] ?? null,
      ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
    }

    // Update order status to "cancellation_requested"
    $this->orderModel->update($orderId, [
      'status' => 'cancellation_requested',
    ]);

    // Auto approve jika masih dalam status yang bisa langsung cancel
    if ($this->canAutoApproveCancellation($order)) {
      $this->autoApproveCancellation($result['refund_id'], $orderId);

      return $this->response->setJSON([
        'success' => true,
        'message' => 'Order berhasil dibatalkan dan refund sedang diproses',
        'data' => [
          'order_id' => $orderId,
          'refund_id' => $result['refund_id'],
          'status' => 'cancelled',
          'refund_status' => 'approved',
          'refund_amount' => $order['total_amount'],
          'auto_approved' => true,
        ],
      ])->setStatusCode(ResponseInterface::HTTP_OK);
    }

    // Kirim notifikasi ke admin untuk review
    $this->sendNotificationToAdmin($result['refund_id']);

    return $this->response->setJSON([
      'success' => true,
      'message' => 'Permintaan pembatalan berhasil diajukan. Admin akan meninjaunya segera.',
      'data' => [
        'order_id' => $orderId,
        'refund_id' => $result['refund_id'],
        'status' => 'cancellation_requested',
        'refund_status' => 'pending',
        'refund_amount' => $order['total_amount'],
        'auto_approved' => false,
      ],
    ])->setStatusCode(ResponseInterface::HTTP_CREATED);
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
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Validation failed',
        'errors' => $this->validator->getErrors(),
      ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
    }

    $json = $this->request->getJSON();
    $orderId = $json->order_id;
    $refundType = $json->refund_type;
    $refundAmount = $json->refund_amount;
    $selectedItems = $json->selected_items ?? [];

    $order = $this->orderModel->find($orderId);

    if (!$order) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Order tidak ditemukan',
      ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
    }

    // Check ownership
    $userId = $this->request->getHeaderLine('X-User-Id') ?? session('user_id');
    if ($order['user_id'] !== $userId) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Unauthorized',
      ])->setStatusCode(ResponseInterface::HTTP_UNAUTHORIZED);
    }

    // Check if order eligible for refund
    if (!$this->isEligibleForRefund($order)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Order tidak eligible untuk refund',
      ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
    }

    // Check if already have active refund request
    if ($this->refundModel->hasActiveRefund($orderId)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Order ini sudah memiliki permintaan refund yang sedang diproses',
      ])->setStatusCode(ResponseInterface::HTTP_CONFLICT);
    }

    // Validasi refund amount
    $maxRefundAmount = $this->calculateMaxRefundAmount($orderId, $refundType, $selectedItems);

    if ($refundAmount > $maxRefundAmount) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Jumlah refund melebihi maksimal yang diperbolehkan',
        'data' => [
          'max_refund_amount' => $maxRefundAmount,
          'requested_amount' => $refundAmount,
        ],
      ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
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
      return $this->response->setJSON([
        'success' => false,
        'message' => $result['message'],
        'errors' => $result['errors'] ?? null,
      ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
    }

    // Update order status
    $this->orderModel->update($orderId, [
      'refund_status' => 'requested',
    ]);

    // Send notification to admin
    $this->sendNotificationToAdmin($result['refund_id']);

    return $this->response->setJSON([
      'success' => true,
      'message' => 'Permintaan refund berhasil dikirim',
      'data' => [
        'order_id' => $orderId,
        'refund_id' => $result['refund_id'],
        'refund_type' => $refundType,
        'refund_amount' => $refundAmount,
        'status' => 'pending',
      ],
    ])->setStatusCode(ResponseInterface::HTTP_CREATED);
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
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Refund tidak ditemukan',
      ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
    }

    return $this->response->setJSON([
      'success' => true,
      'data' => $refund,
    ])->setStatusCode(ResponseInterface::HTTP_OK);
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

    return $this->response->setJSON([
      'success' => true,
      'data' => $refunds,
      'total' => count($refunds),
    ])->setStatusCode(ResponseInterface::HTTP_OK);
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
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Refund tidak ditemukan',
      ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
    }

    // Jika admin adjust amount
    if ($adjustedAmount && $adjustedAmount != $refund['refund_amount']) {
      $this->refundModel->update($refundId, [
        'refund_amount' => $adjustedAmount,
      ]);
    }

    // Approve refund
    if (!$this->refundModel->markApproved($refundId, $adminId, $adminNote)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Gagal approve refund',
      ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
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

    return $this->response->setJSON([
      'success' => true,
      'message' => 'Refund berhasil di-approve',
      'data' => [
        'refund_id' => $refundId,
        'order_id' => $refund['order_id'],
        'status' => 'approved',
        'refund_amount' => $adjustedAmount ?? $refund['refund_amount'],
      ],
    ])->setStatusCode(ResponseInterface::HTTP_OK);
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
      return $this->response->setJSON([
        'success' => false,
        'message' => 'admin_note wajib diisi untuk reject',
      ])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
    }

    $refund = $this->refundModel->find($refundId);

    if (!$refund) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Refund tidak ditemukan',
      ])->setStatusCode(ResponseInterface::HTTP_NOT_FOUND);
    }

    if (!$this->refundModel->markRejected($refundId, $adminId, $adminNote)) {
      return $this->response->setJSON([
        'success' => false,
        'message' => 'Gagal reject refund',
      ])->setStatusCode(ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
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

    return $this->response->setJSON([
      'success' => true,
      'message' => 'Refund berhasil ditolak',
      'data' => [
        'refund_id' => $refundId,
        'order_id' => $refund['order_id'],
        'status' => 'rejected',
        'admin_note' => $adminNote,
      ],
    ])->setStatusCode(ResponseInterface::HTTP_OK);
  }

  // =====================================================
  // HELPER METHODS
  // =====================================================

  private function canBeCancelled($order): bool
  {
    $cancellableStatuses = ['pending', 'confirmed', 'processing', 'packaging'];
    return in_array($order['status'], $cancellableStatuses);
  }

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
