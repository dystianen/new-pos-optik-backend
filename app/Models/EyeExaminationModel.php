<?php

namespace App\Models;

use CodeIgniter\Model;

class EyeExaminationModel extends Model
{
    protected $table            = 'eye_examinations';
    protected $primaryKey       = 'eye_examination_id';
    protected $useAutoIncrement = false;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'eye_examination_id',
        'customer_id',
        'left_eye_sphere',
        'left_eye_cylinder',
        'left_eye_axis',
        'right_eye_sphere',
        'right_eye_cylinder',
        'right_eye_axis',
        'symptoms',
        'diagnosis',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $validationRules = [
        'customer_id'        => 'required',
        'left_eye_sphere'    => 'permit_empty|decimal',
        'left_eye_cylinder'  => 'permit_empty|decimal',
        'left_eye_axis'      => 'permit_empty|integer',
        'right_eye_sphere'   => 'permit_empty|decimal',
        'right_eye_cylinder' => 'permit_empty|decimal',
        'right_eye_axis'     => 'permit_empty|integer',
        'symptoms'           => 'permit_empty',
        'diagnosis'          => 'permit_empty|max_length[100]',
    ];

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['eye_examination_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
