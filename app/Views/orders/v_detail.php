<?= $this->extend('layouts/l_dashboard.php') ?>
<?= $this->section('content') ?>

<?php
function badgeStatus($status)
{
  return match (strtolower($status)) {
    'pending'    => 'badge bg-warning',
    'processing' => 'badge bg-primary',
    'shipped'    => 'badge bg-secondary',
    'completed'  => 'badge bg-success',
    'cancelled'  => 'badge bg-danger',
    default      => 'badge bg-light text-dark'
  };
}
?>


<div class="container-fluid card py-3">
  <div class="card mb-4">
    <div class="card-body d-flex justify-content-between align-items-center">
      <div>
        <h4 class="mb-0">Order #<?= $order['order_id'] ?></h4>
        <small class="text-muted">
          <?= date('d M Y H:i', strtotime($order['order_date'])) ?>
        </small>
      </div>

      <span class="<?= badgeStatus($order['status_code']) ?>">
        <?= strtoupper($order['status_name']) ?>
      </span>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-body">
          <strong>Customer Information</strong>
          <div class="mt-2">
            <p class="mb-1"><strong><?= esc($order['customer_name']) ?></strong></p>
            <p class="mb-1"><?= esc($order['customer_email']) ?></p>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-body">
          <strong>Order Summary</strong>
          <table class="table table-sm mt-2 mb-0">
            <tr>
              <td>Subtotal</td>
              <td class="text-end">Rp <?= number_format($order['grand_total'] - $order['shipping_cost']) ?></td>
            </tr>
            <tr>
              <td>Shipping</td>
              <td class="text-end">Rp <?= number_format($order['shipping_cost']) ?></td>
            </tr>
            <tr class="fw-bold">
              <td>Total</td>
              <td class="text-end">Rp <?= number_format($order['grand_total']) ?></td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>


  <div class="card mb-4">
    <div class="card-body">
      <strong>Order Items</strong>
      <table class="table mt-2 mb-0">
        <thead class="thead-light">
          <tr>
            <th>#</th>
            <th>Product</th>
            <th class="text-center">Qty</th>
            <th class="text-end">Price</th>
            <th class="text-end">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1;
          foreach ($items as $item): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><?= esc($item['product_name']) ?></td>
              <td class="text-center"><?= $item['qty'] ?></td>
              <td class="text-end">Rp <?= number_format($item['price']) ?></td>
              <td class="text-end">Rp <?= number_format($item['price'] * $item['qty']) ?></td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="row mb-4">
    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-body">
          <strong>Shipping</strong>

          <div class="mt-2">
            <p class="mb-1">Method: <strong><?= $order['shipping_method'] ?></strong></p>
            <p class="mb-1">Estimated: <?= $order['estimated_days'] ?> days</p>
            <p class="mb-0"><?= $shippingAddress['address'] ?? '-' ?></p>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card h-100">
        <div class="card-body">
          <strong>Payment</strong>

          <div class="mt-2">
            <p class="mb-1">
              Proof <br>
              <img src="<?= esc($payment['proof']) ?>" class="rounded border w-50 h-100" style="object-fit: contain;" alt="payment">
            </p>
            <p class="mb-1">
              Amount:
              <strong>Rp <?= number_format($payment['amount']) ?? '-' ?></strong>
            </p>
            <p class="mb-1">
              Method: <strong><?= $payment['method_name'] ?? '-' ?></strong>
            </p>
            <p class="mb-1">
              Status:
              <span class="<?= badgeStatus($order['status_code']) ?>">
                <?= strtoupper($order['status_name']) ?>
              </span>
            </p>
            <p class="mb-0">
              Paid At: <?= $payment['paid_at'] ?? '-' ?>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php if (in_array($order['status_code'], ['waiting_confirmation', 'processing'])): ?>
    <div class="card">
      <div class="card-body">
        <h5 class="mb-3">Admin Actions</h5>

        <!-- PAYMENT ACTIONS -->
        <?php if (in_array($order['status_code'], ['waiting_confirmation'])): ?>
          <div class="mb-4">
            <p class="mb-2 fw-semibold">Payment Verification</p>
            <div class="d-flex gap-2 flex-wrap">
              <form method="post" action="<?= base_url('/online-sales/' . $order['order_id'] . '/approve') ?>">
                <?= csrf_field() ?>
                <button type="submit"
                  class="btn btn-success"
                  onclick="return confirm('Approve payment for this order?')">
                  Approve Payment
                </button>
              </form>

              <form method="post" action="<?= base_url('/online-sales/' . $order['order_id'] . '/reject') ?>">
                <?= csrf_field() ?>
                <button type="submit"
                  class="btn btn-danger"
                  onclick="return confirm('Reject payment for this order?')">
                  Reject Payment
                </button>
              </form>
            </div>
          </div>
        <?php endif ?>

        <?php if (in_array($order['status_code'], ['processing'])): ?>
          <div class="card mt-3">
            <div class="card-body">
              <h5 class="mb-3">Shipping Information</h5>

              <form method="post" action="<?= base_url('/online-sales/' . $order['order_id'] . '/ship') ?>">
                <?= csrf_field() ?>

                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label">Courier</label>
                    <select name="courier" class="form-select" required>
                      <option value="">-- Select Courier --</option>
                      <option value="jne">JNE</option>
                      <option value="jnt">J&T</option>
                      <option value="sicepat">SiCepat</option>
                      <option value="anteraja">AnterAja</option>
                      <option value="pos">POS Indonesia</option>
                    </select>
                  </div>

                  <div class="col-md-5">
                    <label class="form-label">Tracking Number</label>
                    <input type="text"
                      name="tracking_number"
                      class="form-control"
                      placeholder="Input resi pengiriman"
                      required>
                  </div>

                  <div class="col-md-auto align-self-end">
                    <button class="btn btn-primary"
                      onclick="return confirm('Confirm shipment & save tracking number?')">
                      Submit Shipment
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        <?php endif ?>
      </div>
    </div>
  <?php endif ?>
</div>
<?= $this->endSection() ?>