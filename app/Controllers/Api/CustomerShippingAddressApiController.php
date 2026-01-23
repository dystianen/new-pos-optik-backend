<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CustomerShippingAddressModel;

class CustomerShippingAddressApiController extends BaseController
{
    protected $csaModel;

    public function __construct()
    {
        $this->csaModel = new CustomerShippingAddressModel();
    }

    // GET /api/shipping
    public function getAllShippingAddress()
    {
        $jwtUser = getJWTUser();
        if (!$jwtUser) {
            return $this->response->setStatusCode(401)->setJSON([
                'message' => 'Unauthorized'
            ]);
        }

        $customerId = $jwtUser->user_id;

        $data = $this->csaModel
            ->where('customer_id', $customerId)
            ->findAll();

        return $this->response->setJSON([
            'status' => 200,
            'message' => 'Get all shipping address successfully!',
            'data' => $data
        ]);
    }

    // GET /api/shipping/{id}
    public function getById($id)
    {
        $data = $this->csaModel->find($id);

        return $this->response->setJSON([
            'status' => 200,
            'message' => 'Get shipping address successfully!',
            'data' => $data
        ]);
    }

    // GET /api/shipping/save
    public function save()
    {
        try {
            $id = $this->request->getVar('id');

            $jwtUser = getJWTUser();
            if (!$jwtUser) {
                return $this->response->setStatusCode(401)->setJSON([
                    'message' => 'Unauthorized'
                ]);
            }

            $customer_id = $jwtUser->user_id;


            $data = [
                'customer_id'    => $customer_id,
                'recipient_name' => $this->request->getVar('recipient_name'),
                'phone'          => $this->request->getVar('phone'),
                'address'        => $this->request->getVar('address'),
                'city'           => $this->request->getVar('city'),
                'province'       => $this->request->getVar('province'),
                'postal_code'    => $this->request->getVar('postal_code'),
            ];

            if (!$this->validate($this->csaModel->validationRules)) {
                return $this->response->setStatusCode(422)->setJSON([
                    'status' => 422,
                    'errors' => $this->validator->getErrors()
                ]);
            }

            if ($id) {
                if (!$this->csaModel->update($id, $data)) {
                    throw new \Exception('Failed to update shipping address');
                }
                $message = 'Shipping address updated successfully!';
            } else {
                if (!$this->csaModel->insert($data)) {
                    throw new \Exception('Failed to create shipping address');
                }
                $message = 'Shipping address created successfully!';
            }

            return $this->response->setJSON([
                'status' => 200,
                'message' => $message
            ]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'message' => $e->getMessage()
            ]);
        }
    }
}
