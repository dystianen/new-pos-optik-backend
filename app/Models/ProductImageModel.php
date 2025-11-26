<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductImageModel extends Model
{
    protected $table            = 'product_images';
    protected $primaryKey       = 'product_image_id';
    protected $useSoftDeletes   = true;

    protected $useAutoIncrement = false;
    protected $insertID         = '';

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'product_image_id',
        'product_id',
        'url',
        'alt_text',
        'sort_order',
        'is_primary',
        'mime_type',
        'size_bytes',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $validationRules = [
        'product_image_id' => 'permit_empty|alpha_numeric_punct|min_length[1]|max_length[36]',
        'product_id'       => 'required|alpha_numeric_punct|min_length[1]|max_length[36]',
        'url'              => 'required|string|max_length[1024]',
        'alt_text'         => 'permit_empty|string|max_length[255]',
        'sort_order'       => 'permit_empty|integer',
        'is_primary'       => 'permit_empty|in_list[0,1]',
        'mime_type'        => 'permit_empty|string|max_length[50]',
        'size_bytes'       => 'permit_empty|integer',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['product_image_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
