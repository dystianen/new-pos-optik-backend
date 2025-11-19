<?php

namespace App\Models;

use CodeIgniter\Model;

class CartItemModel extends Model
{
    protected $table            = 'cart_items';
    protected $primaryKey       = 'cart_item_id';
    protected $useSoftDeletes   = true;

    protected $useAutoIncrement = false;
    protected $insertID         = '';

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'cart_item_id',
        'cart_id',
        'product_id',
        'variant_id',
        'quantity',
        'price',
    ];

    protected $validationRules = [
        'cart_item_id' => 'permit_empty|alpha_numeric_punct|min_length[1]|max_length[36]',
        'cart_id'      => 'required|alpha_numeric_punct|min_length[1]|max_length[36]',
        'product_id'   => 'permit_empty|alpha_numeric_punct|min_length[1]|max_length[36]',
        'variant_id'   => 'permit_empty|alpha_numeric_punct|min_length[1]|max_length[36]',
        'quantity'     => 'required|integer',
        'price'        => 'required|decimal',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['cart_item_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
