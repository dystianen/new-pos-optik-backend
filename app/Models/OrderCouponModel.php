<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class OrderCouponModel extends Model
{
    protected $table = 'order_coupons';
    protected $primaryKey = 'order_coupon_id';
    protected $useAutoIncrement = false;

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'order_coupon_id',
        'order_id',
        'coupon_id',
        'discount_amount',
    ];

    protected $beforeInsert = ['generateUUID'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data'][$this->primaryKey])) {
            $data['data'][$this->primaryKey] = Uuid::uuid4()->toString();
        }
        return $data;
    }
}
