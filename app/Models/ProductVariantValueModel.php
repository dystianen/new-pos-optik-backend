<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductVariantValueModel extends Model
{
    protected $table            = 'product_variant_values';
    protected $primaryKey       = 'pv_value_id';
    protected $useSoftDeletes   = true;

    protected $useAutoIncrement = false;
    protected $insertID         = '';

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'pv_value_id',
        'variant_id',
        'pav_id',
    ];

    protected $validationRules = [
        'variant_id' => 'required|alpha_numeric_punct|min_length[1]|max_length[36]',
        'pav_id'     => 'required|alpha_numeric_punct|min_length[1]|max_length[36]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['pv_value_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
