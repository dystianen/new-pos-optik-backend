<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemLensModel extends Model
{
    protected $table            = 'order_item_lens';
    protected $primaryKey       = 'order_item_lens_id';
    protected $useAutoIncrement = false;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'order_item_lens_id',
        'order_item_id',
        'lens_type_id',
        'price_addon',
    ];

    protected $validationRules = [
        'order_item_id' => 'required',
        'lens_type_id'  => 'required',
        'price_addon'   => 'decimal',
    ];

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['order_item_lens_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
