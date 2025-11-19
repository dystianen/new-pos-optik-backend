<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class WishlistModel extends Model
{
    protected $table = 'wishlists';
    protected $primaryKey = 'wishlist_id';
    protected $useAutoIncrement = false;

    protected $allowedFields = [
        'wishlist_id',
        'customer_id',
        'product_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
    protected $useSoftDeletes = true;

    // Auto-generate UUID
    protected $beforeInsert = ['generateUUID'];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data'][$this->primaryKey])) {
            $data['data'][$this->primaryKey] = Uuid::uuid4()->toString();
        }
        return $data;
    }
}
