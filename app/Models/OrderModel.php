<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'order_id';
    protected $useSoftDeletes   = true;

    protected $useAutoIncrement = false;
    protected $insertID         = '';

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'order_id',
        'customer_id',
        'status_id',
        'shipping_method_id',
        'shipping_cost',
        'shipping_discount',
        'coupon_discount',
        'grand_total',
    ];

    protected $validationRules = [
        'order_id'            => 'permit_empty|alpha_numeric_punct|min_length[1]|max_length[36]',
        'customer_id'         => 'permit_empty|alpha_numeric_punct|min_length[1]|max_length[36]',
        'status_id'           => 'permit_empty|alpha_numeric_punct|min_length[1]|max_length[36]',
        'shipping_method_id'  => 'permit_empty|alpha_numeric_punct|min_length[1]|max_length[36]',

        'shipping_cost'       => 'permit_empty|decimal',
        'shipping_discount'   => 'permit_empty|decimal',
        'coupon_discount'     => 'permit_empty|decimal',
        'grand_total'         => 'required|decimal',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['order_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
