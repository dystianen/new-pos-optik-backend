<?php

namespace App\Models;

use CodeIgniter\Model;

class LensTypeModel extends Model
{
    protected $table            = 'lens_types';
    protected $primaryKey       = 'lens_type_id';
    protected $useSoftDeletes   = true;

    protected $useAutoIncrement = false;
    protected $insertID         = '';

    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'lens_type_id',
        'lens_type_name',
        'description',
        'price_addon',
    ];

    protected $validationRules = [
        'lens_type_id'   => 'permit_empty|alpha_numeric_punct|min_length[1]|max_length[36]',
        'lens_type_name' => 'required|string|max_length[50]',
        'description'    => 'permit_empty',
        'price_addon'    => 'permit_empty|decimal',
    ];

    protected $validationMessages = [];
    protected $skipValidation     = false;

    protected $beforeInsert = ['generateUuid'];

    protected function generateUuid(array $data)
    {
        $data['data']['lens_type_id'] = service('uuid')->uuid4()->toString();
        return $data;
    }
}
