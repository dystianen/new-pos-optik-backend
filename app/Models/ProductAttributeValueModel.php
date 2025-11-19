<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductAttributeValueModel extends Model
{
    protected $table            = 'product_attribute_values';
    protected $primaryKey       = 'pav_id';
    protected $useSoftDeletes   = true;

    protected $useAutoIncrement = false;
    protected $insertID         = '';

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'pav_id',
        'product_id',
        'attribute_id',
        'value',
    ];

    protected $validationRules = [
        'pav_id'       => 'permit_empty|alpha_numeric_punct|min_length[1]|max_length[36]',
        'product_id'   => 'required|alpha_numeric_punct|min_length[1]|max_length[36]',
        'attribute_id' => 'required|alpha_numeric_punct|min_length[1]|max_length[36]',
        'value'        => 'required|string|max_length[100]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['pav_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
