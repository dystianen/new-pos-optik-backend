<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'payment_id';
    protected $useAutoIncrement = false;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'payment_id',
        'order_id',
        'payment_method_id',
        'amount',
        'proof',
        'paid_at',
    ];

    protected $validationRules = [
        'order_id'           => 'required',
        'payment_method_id'  => 'required',
        'amount'             => 'required|decimal',
        'proof'              => 'permit_empty',
        'paid_at'            => 'permit_empty|valid_date',
    ];

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['payment_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
