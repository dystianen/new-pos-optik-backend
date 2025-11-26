<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductAttributeMasterValueModel extends Model
{
    protected $table            = 'product_attribute_master_values';
    protected $primaryKey       = 'attribute_master_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'attribute_master_id',
        'attribute_id',
        'value',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['attribute_master_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
