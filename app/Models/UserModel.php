<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = false;

    protected $allowedFields = [
        'user_id',
        'user_name',
        'user_email',
        'password',
        'role_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    // Auto generate UUID
    protected $beforeInsert = ['generateUUID'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data'][$this->primaryKey])) {
            $data['data'][$this->primaryKey] = Uuid::uuid4()->toString();
        }
        return $data;
    }
}
