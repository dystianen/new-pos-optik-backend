<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryTransactionModel extends Model
{
    protected $table            = 'inventory_transactions';
    protected $primaryKey       = 'inventory_transaction_id';
    protected $useAutoIncrement = false;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'inventory_transaction_id',
        'product_id',
        'variant_id',
        'transaction_type',
        'quantity',
        'transaction_date',
        'description',
        'user_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $validationRules = [
        'product_id'        => 'required',
        'transaction_type'  => 'required|in_list[in,out]',
        'quantity'          => 'required|integer',
        'transaction_date'  => 'required|valid_date',
        'description'       => 'permit_empty',
        'user_id'           => 'permit_empty',
    ];

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['inventory_transaction_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
