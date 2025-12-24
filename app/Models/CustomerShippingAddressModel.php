<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerShippingAddressModel extends Model
{
    protected $table            = 'customer_shipping_addresses';
    protected $primaryKey       = 'csa_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';

    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;

    protected $allowedFields = [
        'csa_id',
        'customer_id',
        'recipient_name',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['csa_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
