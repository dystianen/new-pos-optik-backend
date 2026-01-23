<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\UserRefundAccountModel;

class UserRefundAccountApiController extends BaseController
{
    protected $refundModel;

    public function __construct()
    {
        $this->refundModel = new UserRefundAccountModel();
    }

    // =======================
    // API FUNCTIONS
    // =======================

    // GET /api/refund-accounts
    public function findOne()
    {
        $jwtUser = getJWTUser();
        $userId = $jwtUser->user_id;

        $data = $this->refundModel
            ->where('customer_id', $userId)
            ->first();

        return $this->response->setJSON([
            'status'  => 200,
            'message' => 'Get refund accounts successfully!',
            'data'    => $data
        ]);
    }

    // GET /api/refund-accounts/{id}
    public function getById($id)
    {
        $jwtUser = getJWTUser();
        if (!$jwtUser) {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Unauthorized'
            ]);
        }

        $data = $this->refundModel
            ->where('user_refund_account_id', $id)
            ->where('customer_id', $jwtUser->user_id)
            ->first();

        if (!$data) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Refund account not found'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 200,
            'message' => 'Get refund account successfully!',
            'data'    => $data
        ]);
    }

    // POST /api/refund-accounts/save
    public function save()
    {
        try {
            $jwtUser = getJWTUser();
            if (!$jwtUser) {
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Unauthorized'
                ]);
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
                return $this->response->setStatusCode(422)->setJSON([
                    'status' => 422,
                    'errors' => $this->validator->getErrors()
                ]);
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
                    throw new \Exception('Failed to update refund account');
                }

                $message = 'Refund account updated successfully!';
            } else {
                // INSERT
                $success = $this->refundModel->insert($data);

                if (!$success) {
                    throw new \Exception('Failed to create refund account');
                }

                $message = 'Refund account created successfully!';
            }

            return $this->response->setJSON([
                'status'  => 200,
                'message' => $message
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'message' => $e->getMessage()
            ]);
        }
    }

    // DELETE /api/refund-accounts/{id}
    public function delete($id)
    {
        $jwtUser = getJWTUser();
        if (!$jwtUser) {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Unauthorized'
            ]);
        }

        $data = $this->refundModel
            ->where('user_refund_account_id', $id)
            ->where('customer_id', $jwtUser->user_id)
            ->first();

        if (!$data) {
            return $this->response->setStatusCode(404)->setJSON([
                'message' => 'Refund account not found'
            ]);
        }

        $this->refundModel->delete($id);

        return $this->response->setJSON([
            'status'  => 200,
            'message' => 'Refund account deleted successfully!'
        ]);
    }
}
