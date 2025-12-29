<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemPrescriptionModel extends Model
{
  protected $table            = 'order_item_prescriptions';
  protected $primaryKey       = 'order_item_prescription_id';
  protected $useAutoIncrement = false;
  protected $returnType       = 'array';

  protected $allowedFields = [
    'order_item_prescription_id',
    'order_item_id',

    'right_sph',
    'right_cyl',
    'right_axis',
    'right_add',

    'left_sph',
    'left_cyl',
    'left_axis',
    'left_add',

    'pd_single',
    'pd_left',
    'pd_right',

    'created_at',
    'updated_at',
    'deleted_at'
  ];

  protected $useTimestamps = true;
  protected $createdField  = 'created_at';
  protected $updatedField  = 'updated_at';
  protected $deletedField = 'deleted_at';

  /**
   * Generate UUID jika belum diset
   */
  protected $beforeInsert = ['generateUUID'];

  protected function generateUuid(array $data)
  {
    $data['data']['order_item_prescription_id'] = service('uuid')->uuid4()->toString();
    return $data;
  }
}
