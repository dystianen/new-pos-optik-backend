<?php

namespace App\Controllers\Api;

use App\Models\UserRefundAccountModel;

class UserRefundAccountApiController extends BaseApiController
{
    protected $refundModel;

    public function __construct()
    {
        $this->refundModel = new UserRefundAccountModel();
    }

    // GET /api/refund-accounts
    public function findOne()
    {
        $jwtUser = getJWTUser();
        $userId = $jwtUser->user_id;

        $data = $this->refundModel
            ->where('customer_id', $userId)
            ->first();

        return $this->successResponse($data);
    }

    // GET /api/refund-accounts/{id}
    public function getById($id)
    {
        $jwtUser = getJWTUser();
        if (!$jwtUser) {
            return $this->unauthorizedResponse();
        }

        $data = $this->refundModel
            ->where('user_refund_account_id', $id)
            ->where('customer_id', $jwtUser->user_id)
            ->first();

        if (!$data) {
            return $this->errorResponse('Refund account not found');
        }

        return $this->successResponse($data);
    }

    // POST /api/refund-accounts/save
    public function save()
    {
        try {
            $jwtUser = getJWTUser();
            if (!$jwtUser) {
                return $this->unauthorizedResponse();
            }

            $customerId = $jwtUser->user_id;

            $data = [
                'customer_id'    => $customerId,
                'account_name'   => $this->request->getVar('account_name'),
                'bank_name'      => $this->request->getVar('bank_name'),
                'account_number' => $this->request->getVar('account_number'),
                'is_default'     => true, // selalu true karena cuma 1
            ];

            if (!$this->validate($this->refundModel->validationRules)) {
                return $this->validationErrorResponse($this->validator->getErrors());
            }

            // 🔍 cari berdasarkan customer_id
            $existing = $this->refundModel
                ->where('customer_id', $customerId)
                ->first();

            if ($existing) {
                // UPDATE
                $success = $this->refundModel->update(
                    $existing['user_refund_account_id'],
                    $data
                );

                if (!$success) {
                    return $this->errorResponse('Failed to update refund account');
                }

                $message = 'Refund account updated successfully!';
            } else {
                // INSERT
                $success = $this->refundModel->insert($data);

                if (!$success) {
                    return $this->errorResponse('Failed to create refund account');
                }

                $message = 'Refund account created successfully!';
            }

            return $this->messageResponse($message);
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    // DELETE /api/refund-accounts/{id}
    public function deleteAccount($id)
    {
        $customerId = $this->getAuthenticatedCustomerId();

        $data = $this->refundModel
            ->where('user_refund_account_id', $id)
            ->where('customer_id', $customerId)
            ->first();

        if (!$data) {
            return $this->notFoundResponse('Refund account not found');
        }

        $this->refundModel->delete($id);

        return $this->messageResponse('Refund account deleted successfully!');
    }
}
