<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductVariantAttributeModel extends Model
{
    protected $table            = 'product_variant_attributes';
    protected $primaryKey       = 'pva_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';

    protected $useSoftDeletes   = false;

    protected $useTimestamps    = false;
    protected $createdField     = 'created_at';

    protected $allowedFields = [
        'pva_id',
        'product_id',
        'attribute_id',
        'created_at',
    ];

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['pva_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
