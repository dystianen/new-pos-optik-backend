<?php

namespace App\Models;

use CodeIgniter\Model;

class LensPrescriptionModel extends Model
{
    protected $table            = 'lens_prescriptions';
    protected $primaryKey       = 'prescription_id';
    protected $useSoftDeletes   = true;

    protected $useAutoIncrement = false;
    protected $insertID         = '';

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'prescription_id',
        'order_item_id',
        'left_sphere',
        'left_cylinder',
        'left_axis',
        'right_sphere',
        'right_cylinder',
        'right_axis',
        'pd',
    ];

    protected $validationRules = [
        'prescription_id' => 'permit_empty|alpha_numeric_punct|min_length[1]|max_length[36]',
        'order_item_id'   => 'permit_empty|alpha_numeric_punct|min_length[1]|max_length[36]',

        'left_sphere'     => 'permit_empty|numeric',
        'left_cylinder'   => 'permit_empty|numeric',
        'left_axis'       => 'permit_empty|integer',

        'right_sphere'    => 'permit_empty|numeric',
        'right_cylinder'  => 'permit_empty|numeric',
        'right_axis'      => 'permit_empty|integer',

        'pd'              => 'permit_empty|integer',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['prescription_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
