<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentMethodModel extends Model
{
    protected $table            = 'payment_methods';
    protected $primaryKey       = 'payment_method_id';
    protected $useAutoIncrement = false;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'payment_method_id',
        'method_name',
        'method_type',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $validationRules = [
        'method_name' => 'required|max_length[100]',
        'method_type' => 'permit_empty|max_length[50]',
        'is_active'   => 'permit_empty|in_list[0,1]',
    ];

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['payment_method_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
