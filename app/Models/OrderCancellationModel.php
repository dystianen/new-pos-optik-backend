<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderCancellationModel extends Model
{
    protected $table            = 'order_cancellations';
    protected $primaryKey       = 'order_cancellation_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'order_cancellation_id',
        'order_id',
        'reason',
        'additional_note',
        'status',
        'processed_by',
        'processed_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // =====================
    // STATUS CONSTANTS
    // =====================
    public const STATUS_PENDING    = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_APPROVED   = 'approved';
    public const STATUS_REJECTED   = 'rejected';

    // Callbacks
    protected $beforeInsert = ['generateID'];

    protected function generateID(array $data)
    {
        if (! isset($data['data']['order_cancellation_id'])) {
            $data['data']['order_cancellation_id'] = service('uuid')->uuid4()->toString();
        }

        return $data;
    }
}
