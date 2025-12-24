<?php

namespace App\Models;

use CodeIgniter\Model;

class CartItemPrescriptionModel extends Model
{
    protected $table            = 'cart_item_prescriptions';
    protected $primaryKey       = 'prescription_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'prescription_id',
        'cart_item_id',

        // RIGHT EYE (OD)
        'right_sph',
        'right_cyl',
        'right_axis',
        'right_add',

        // LEFT EYE (OS)
        'left_sph',
        'left_cyl',
        'left_axis',
        'left_add',

        // PD
        'pd_single',
        'pd_left',
        'pd_right',

        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate UUID automatically
     */
    protected function generateUUID()
    {
        return service('uuid')->uuid4()->toString();
    }

    /**
     * Before insert hook
     */
    protected $beforeInsert = ['setUUID'];

    protected function setUUID(array $data)
    {
        if (!isset($data['data']['prescription_id'])) {
            $data['data']['prescription_id'] = $this->generateUUID();
        }

        return $data;
    }

    /**
     * Get prescription by cart item
     */
    public function getByCartItem(string $cartItemId)
    {
        return $this->where('cart_item_id', $cartItemId)->first();
    }
}
