<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderRefundModel extends Model
{
    protected $table            = 'order_refunds';
    protected $primaryKey       = 'order_refund_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'order_refund_id',
        'order_id',
        'user_refund_account_id',
    ];

    protected $useTimestamps = true;

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['order_refund_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }

    public function findByOrder(string $orderId)
    {
        return $this->where('order_id', $orderId)->first();
    }
}
