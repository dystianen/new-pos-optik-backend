<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table            = 'customers';
    protected $primaryKey       = 'customer_id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['customer_name', 'customer_email', 'customer_password', 'customer_phone', 'customer_dob', 'customer_gender', 'customer_occupation', 'customer_eye_history', 'customer_preferences'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['customer_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
