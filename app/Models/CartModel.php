<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table            = 'carts';
    protected $primaryKey       = 'cart_id';
    protected $useSoftDeletes   = true;

    protected $useAutoIncrement = false;
    protected $insertID         = '';

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'cart_id',
        'customer_id',
        'status',
    ];

    protected $validationRules = [
        'cart_id'     => 'permit_empty|alpha_numeric_punct|min_length[1]|max_length[36]',
        'customer_id' => 'permit_empty|alpha_numeric_punct|min_length[1]|max_length[36]',
        'status'      => 'required|in_list[active,abandoned,saved]',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['cart_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
