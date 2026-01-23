<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderRefundModel extends Model
{
    protected $table            = 'order_refunds';
    protected $primaryKey       = 'order_refund_id';
    protected $useAutoIncrement = false;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'order_refund_id',
        'order_id',
        'type',
        'user_refund_account_id',
        'refund_amount',
        'reason',
        'status',
        'admin_note',
        'processed_by',
        'completed_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // =====================
    // VALIDATION RULES
    // =====================
    protected $validationRules = [
        'order_id' => 'required|min_length[36]|max_length[36]',
        'status'   => 'required|in_list[pending,processing,approved,rejected,cancelled]',
        'refund_amount' => 'permit_empty|decimal|greater_than[0]',
    ];

    protected $validationMessages = [
        'order_id' => [
            'required' => 'Order ID harus diisi',
        ],
        'status' => [
            'in_list' => 'Status tidak valid',
        ],
        'refund_amount' => [
            'decimal' => 'Jumlah refund harus berupa angka',
            'greater_than' => 'Jumlah refund harus lebih dari 0',
        ],
    ];

    // =====================
    // STATUS CONSTANTS
    // =====================
    public const STATUS_PENDING    = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_APPROVED   = 'approved';
    public const STATUS_REJECTED   = 'rejected';

    // =====================
    // TYPE CONSTANTS
    // =====================
    public const TYPE_CANCEL = 'cancel';
    public const TYPE_REFUND = 'refund';

    // =====================
    // CALLBACKS
    // =====================
    protected $beforeInsert = ['generateUUID'];
    protected $beforeUpdate = [];

    protected function generateUUID(array $data)
    {
        if (!isset($data['data']['order_refund_id'])) {
            $data['data']['order_refund_id'] = $this->generateUUIDv4();
        }
        return $data;
    }

    private function generateUUIDv4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    // =====================
    // RELATIONSHIP METHODS
    // =====================
    public function withOrder()
    {
        return $this->select('order_refunds.*, orders.*')
            ->join('orders', 'orders.order_id = order_refunds.order_id', 'left');
    }

    public function withRefundAccount()
    {
        return $this->select('order_refunds.*, user_refund_accounts.*')
            ->join('user_refund_accounts', 'user_refund_accounts.user_refund_account_id = order_refunds.user_refund_account_id', 'left');
    }

    public function withProcessedBy()
    {
        return $this->select('order_refunds.*, users.name as admin_name, users.email as admin_email')
            ->join('users', 'users.user_id = order_refunds.processed_by', 'left');
    }

    public function withAll()
    {
        return $this->select('order_refunds.*, 
                             orders.order_number, orders.total_amount as order_amount,
                             user_refund_accounts.account_name, user_refund_accounts.account_number,
                             users.name as admin_name')
            ->join('orders', 'orders.order_id = order_refunds.order_id', 'left')
            ->join('user_refund_accounts', 'user_refund_accounts.user_refund_account_id = order_refunds.user_refund_account_id', 'left')
            ->join('users', 'users.user_id = order_refunds.processed_by', 'left');
    }

    // =====================
    // QUERY HELPERS
    // =====================
    public function getByOrderId(string $orderId)
    {
        return $this->where('order_id', $orderId)->findAll();
    }

    public function getByStatus(string $status)
    {
        return $this->where('status', $status)->findAll();
    }

    public function getByType(string $type)
    {
        return $this->where('type', $type)->findAll();
    }

    public function getCancellations()
    {
        return $this->where('type', self::TYPE_CANCEL)->findAll();
    }

    public function getRefunds()
    {
        return $this->where('type', self::TYPE_REFUND)->findAll();
    }

    public function getPendingRefunds()
    {
        return $this->where('status', self::STATUS_PENDING)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }

    public function hasActiveRefund(string $orderId): bool
    {
        return $this->where('order_id', $orderId)
            ->whereIn('status', [self::STATUS_PENDING, self::STATUS_PROCESSING])
            ->countAllResults() > 0;
    }

    // =====================
    // STATUS UPDATE METHODS
    // =====================
    public function markProcessing(string $id, string $adminId = null)
    {
        $data = [
            'status' => self::STATUS_PROCESSING,
        ];

        if ($adminId) {
            $data['processed_by'] = $adminId;
        }

        return $this->update($id, $data);
    }

    public function markApproved(string $id, string $adminId = null, string $note = null)
    {
        $data = [
            'status' => self::STATUS_APPROVED,
            'completed_at' => date('Y-m-d H:i:s'),
        ];

        if ($adminId) {
            $data['processed_by'] = $adminId;
        }

        if ($note) {
            $data['admin_note'] = $note;
        }

        return $this->update($id, $data);
    }

    public function markRejected(string $id, string $adminId = null, string $note = null)
    {
        $data = [
            'status' => self::STATUS_REJECTED,
            'completed_at' => date('Y-m-d H:i:s'),
        ];

        if ($adminId) {
            $data['processed_by'] = $adminId;
        }

        if ($note) {
            $data['admin_note'] = $note;
        }

        return $this->update($id, $data);
    }

    // =====================
    // STATISTICS METHODS
    // =====================
    public function getRefundStats(string $startDate = null, string $endDate = null)
    {
        $builder = $this->builder();

        if ($startDate) {
            $builder->where('created_at >=', $startDate);
        }

        if ($endDate) {
            $builder->where('created_at <=', $endDate);
        }

        return [
            'total' => $builder->countAllResults(false),
            'pending' => $builder->where('status', self::STATUS_PENDING)->countAllResults(false),
            'processing' => $builder->where('status', self::STATUS_PROCESSING)->countAllResults(false),
            'approved' => $builder->where('status', self::STATUS_APPROVED)->countAllResults(false),
            'rejected' => $builder->where('status', self::STATUS_REJECTED)->countAllResults(false),
            'total_amount' => $builder->selectSum('refund_amount')->get()->getRow()->refund_amount ?? 0,
        ];
    }

    // =====================
    // BUSINESS LOGIC
    // =====================
    public function createRefund(array $data)
    {
        // Cek apakah order sudah punya refund yang masih aktif
        if ($this->hasActiveRefund($data['order_id'])) {
            return [
                'success' => false,
                'message' => 'Order ini sudah memiliki permintaan refund yang sedang diproses',
            ];
        }

        // Set default status & type
        $data['status'] = self::STATUS_PENDING;
        if (!isset($data['type'])) {
            $data['type'] = self::TYPE_REFUND;
        }

        if ($this->insert($data)) {
            return [
                'success' => true,
                'message' => 'Permintaan refund berhasil dibuat',
                'refund_id' => $this->getInsertID(),
            ];
        }

        return [
            'success' => false,
            'message' => 'Gagal membuat permintaan refund',
            'errors' => $this->errors(),
        ];
    }

    public function createCancellation(array $data)
    {
        // Cek apakah order sudah punya cancel request yang masih aktif
        if ($this->hasActiveRefund($data['order_id'])) {
            return [
                'success' => false,
                'message' => 'Order ini sudah memiliki permintaan pembatalan yang sedang diproses',
            ];
        }

        // Set type to cancel
        $data['type'] = self::TYPE_CANCEL;
        $data['status'] = self::STATUS_PENDING;

        if ($this->insert($data)) {
            return [
                'success' => true,
                'message' => 'Permintaan pembatalan berhasil dibuat',
                'refund_id' => $this->getInsertID(),
            ];
        }

        return [
            'success' => false,
            'message' => 'Gagal membuat permintaan pembatalan',
            'errors' => $this->errors(),
        ];
    }
}
