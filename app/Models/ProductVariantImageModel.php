<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductVariantImageModel extends Model
{
    protected $table            = 'product_variant_images';
    protected $primaryKey       = 'pv_image_id';
    protected $useSoftDeletes   = true;

    protected $useAutoIncrement = false;
    protected $insertID         = '';

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $deletedField     = 'deleted_at';
    protected $updatedField     = 'updated_at';

    protected $allowedFields = [
        'pv_image_id',
        'variant_id',
        'product_image_id',
    ];

    protected $validationRules = [
        'pv_image_id' => 'permit_empty|alpha_numeric_punct|min_length[1]|max_length[36]',
        'variant_id'       => 'required|alpha_numeric_punct|min_length[1]|max_length[36]',
        'product_image_id' => 'required|alpha_numeric_punct|min_length[1]|max_length[36]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['pv_image_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
