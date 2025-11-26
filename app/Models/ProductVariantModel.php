<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductVariantModel extends Model
{
    protected $table            = 'product_variants';
    protected $primaryKey       = 'variant_id';
    protected $useSoftDeletes   = true;

    protected $useAutoIncrement = false;
    protected $insertID         = '';

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'variant_id',
        'product_id',
        'variant_name',
        'price',
        'stock',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $validationRules = [
        'variant_id'    => 'permit_empty|alpha_numeric_punct|min_length[1]|max_length[36]',
        'product_id'    => 'required|alpha_numeric_punct|min_length[1]|max_length[36]',
        'variant_name'  => 'required|string|max_length[100]',
        'price'         => 'decimal',
        'stock'         => 'integer',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['variant_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
