<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class CustomerModel extends Model
{
    protected $table            = 'customers';
    protected $primaryKey       = 'customer_id';
    protected $useAutoIncrement = false;
    protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_password',
        'customer_phone',
        'customer_dob',
        'customer_gender',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    // timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'customer_name'     => 'required|max_length[100]',
        'customer_email'    => 'required|valid_email|max_length[100]',
        'customer_password' => 'required|max_length[255]',
        'customer_phone'    => 'permit_empty|max_length[20]',
        'customer_dob'      => 'permit_empty|valid_date',
        'customer_gender'   => 'permit_empty|in_list[male,female,other]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    // UUID Generator
    protected $beforeInsert = ['generateUUID'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data'][$this->primaryKey])) {
            $data['data'][$this->primaryKey] = Uuid::uuid4()->toString();
        }
        return $data;
    }
}
