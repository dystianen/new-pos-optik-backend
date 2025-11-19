<?php

namespace App\Models;

use CodeIgniter\Model;

class ShippingMethodModel extends Model
{
    protected $table            = 'shipping_methods';
    protected $primaryKey       = 'shipping_method_id';
    protected $useAutoIncrement = false;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'shipping_method_id',
        'name',
        'provider',
        'estimated_days',
        'is_active',
    ];

    protected $validationRules = [
        'name'           => 'required|max_length[100]',
        'provider'       => 'permit_empty|max_length[50]',
        'estimated_days' => 'permit_empty|max_length[20]',
        'is_active'      => 'permit_empty|in_list[0,1]',
    ];

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['shipping_method_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
