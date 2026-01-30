<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderRefundItemModel extends Model
{
  protected $table            = 'order_refund_items';
  protected $primaryKey       = 'order_refund_item_id';
  protected $useAutoIncrement = false;

  protected $returnType     = 'array';
  protected $useSoftDeletes = false;

  protected $allowedFields = [
    'order_refund_item_id',
    'order_refund_id',
    'order_item_id',
    'qty_refunded',
    'price_per_item',
    'subtotal_refunded',
  ];

  protected $useTimestamps = true;
  protected $createdField  = 'created_at';
  protected $updatedField  = 'updated_at';

  // =====================
  // CALLBACKS
  // =====================
  protected $beforeInsert = ['generateUUID'];

  protected function generateUuid(array $data)
  {
    $data['data']['order_refund_item_id'] = service('uuid')->uuid4()->toString();
    return $data;
  }

  // =====================
  // RELATIONSHIP METHODS
  // =====================
  public function withOrderItem()
  {
    return $this->select('order_refund_items.*,
                         products.product_name,
                         product_variants.variant_name,
                         order_items.quantity,
                         order_items.price as price_purchased')
      ->join('order_items', 'order_items.order_item_id = order_refund_items.order_item_id', 'left')
      ->join('products', 'products.product_id = order_items.product_id', 'left')
      ->join('product_variants', 'product_variants.variant_id = order_items.variant_id', 'left');
  }

  // =====================
  // QUERY HELPERS
  // =====================
  public function getByRefundId(string $refundId)
  {
    return $this->withOrderItem()
      ->where('order_refund_items.order_refund_id', $refundId)
      ->findAll();
  }

  public function getTotalRefundedAmount(string $refundId)
  {
    return $this->selectSum('subtotal_refunded')
      ->where('order_refund_id', $refundId)
      ->get()
      ->getRow()
      ->subtotal_refunded ?? 0;
  }
}
