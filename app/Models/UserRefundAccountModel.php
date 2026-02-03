<?php

namespace App\Models;

use CodeIgniter\Model;

class UserRefundAccountModel extends Model
{
    protected $table            = 'user_refund_accounts';
    protected $primaryKey       = 'user_refund_account_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'user_refund_account_id',
        'customer_id',
        'account_name',
        'bank_name',
        'account_number',
        'is_default',
    ];

    protected $validationRules = [
        'account_name'   => 'required|min_length[3]',
        'bank_name'      => 'required',
        'account_number' => 'required|min_length[5]',
    ];

    protected $useTimestamps = true;

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        if (!isset($data['data']['user_refund_account_id'])) {
            $data['data']['user_refund_account_id'] = service('uuid')->uuid4()->toString();
        }
        return $data;
    }

    public function findByOrder(string $orderId)
    {
        return $this->where('order_id', $orderId)->first();
    }

    /**
     * Get user's refund accounts
     */
    public function getByUser(string $userId)
    {
        return $this->where('customer_id', $userId)->findAll();
    }

    /**
     * Set default refund account
     */
    public function setDefault(string $userId, string $accountId)
    {
        $this->where('customer_id', $userId)->set(['is_default' => false])->update();

        return $this->update($accountId, ['is_default' => true]);
    }
}
