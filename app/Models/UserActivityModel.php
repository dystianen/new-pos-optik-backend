<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class UserActivityModel extends Model
{
    protected $table = 'user_activities';
    protected $primaryKey = 'user_activity_id';
    protected $useAutoIncrement = false;

    protected $allowedFields = [
        'user_activity_id',
        'customer_id',
        'product_id',
        'activity_type',
        'activity_details',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    // Generate UUID otomatis
    protected $beforeInsert = ['generateUUID'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data'][$this->primaryKey])) {
            $data['data'][$this->primaryKey] = Uuid::uuid4()->toString();
        }
        return $data;
    }
}
