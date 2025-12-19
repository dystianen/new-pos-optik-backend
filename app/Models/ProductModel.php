<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'product_id';
    protected $useSoftDeletes   = true;

    protected $useAutoIncrement = false;
    protected $insertID         = '';

    // timestamps
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $allowedFields = [
        'product_id',
        'category_id',
        'product_name',
        'product_price',
        'product_stock',
        'product_brand',
        'description',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $validationRules = [
        'product_id'    => 'permit_empty|alpha_numeric_punct|min_length[1]|max_length[36]',
        'category_id'   => 'permit_empty|alpha_numeric_punct|min_length[1]|max_length[36]',
        'product_name'  => 'required|string|max_length[100]',
        'product_price' => 'required|decimal',
        'product_brand' => 'permit_empty|string|max_length[50]',
        'description'   => 'permit_empty',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;

    protected $beforeInsert = ['generateUUID'];
    protected function generateUuid(array $data)
    {
        $data['data']['product_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
