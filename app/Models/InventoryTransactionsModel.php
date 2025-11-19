<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryTransactionsModel extends Model
{
    protected $table            = 'inventory_transactions';
    protected $primaryKey       = 'inventory_transaction_id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['product_id', 'transaction_type', 'quantity', 'transaction_date', 'description', 'user_id', 'created_at', 'updated_at'];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['inventory_transaction_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
