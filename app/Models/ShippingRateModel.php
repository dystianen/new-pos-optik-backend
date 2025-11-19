<?php

namespace App\Models;

use CodeIgniter\Model;

class ShippingRateModel extends Model
{
    protected $table            = 'shipping_rates';
    protected $primaryKey       = 'rate_id';
    protected $useAutoIncrement = false;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'rate_id',
        'shipping_method_id',
        'destination',
        'cost',
    ];

    protected $validationRules = [
        'shipping_method_id' => 'required',
        'destination'        => 'required|max_length[200]',
        'cost'               => 'required|decimal',
    ];

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['rate_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
