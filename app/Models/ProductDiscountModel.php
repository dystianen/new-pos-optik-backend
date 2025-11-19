<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductDiscountModel extends Model
{
    protected $table            = 'product_discounts';
    protected $primaryKey       = 'product_discount_id';
    protected $useAutoIncrement = false;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'product_discount_id',
        'product_id',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $validationRules = [
        'product_id'      => 'required',
        'discount_type'   => 'required|max_length[20]',
        'discount_value'  => 'required|decimal',
        'start_date'      => 'required|valid_date',
        'end_date'        => 'required|valid_date',
        'is_active'       => 'permit_empty|in_list[0,1]',
    ];

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['product_discount_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
