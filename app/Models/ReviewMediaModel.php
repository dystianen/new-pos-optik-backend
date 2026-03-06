<?php

namespace App\Models;

use CodeIgniter\Model;

class ReviewMediaModel extends Model
{
    protected $table            = 'review_media';
    protected $primaryKey       = 'review_media_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'review_media_id',
        'review_id',
        'file_url',
        'file_type',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['review_media_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
