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
    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-header fw-bold">
          Shipping
        </div>
        <div class="card-body">
          <dl class="row mb-0 small">
            <dt class="col-5 text-muted">Method</dt>
            <dd class="col-7 fw-semibold"><?= $order['shipping_method'] ?></dd>

            <dt class="col-5 text-muted">Estimated</dt>
            <dd class="col-7"><?= $order['estimated_days'] ?> days</dd>

            <dt class="col-5 text-muted">Courier</dt>
            <dd class="col-7"><?= $order['courier'] ?></dd>

            <dt class="col-5 text-muted">Tracking</dt>
            <dd class="col-7 fw-semibold"><?= $order['tracking_number'] ?: '-' ?></dd>

            <dt class="col-12 text-muted mt-2">Address</dt>
            <dd class="col-12 mb-0"><?= $shippingAddress['address'] ?? '-' ?></dd>
          </dl>
        </div>
      </div>
    </div>

    <?php if ($payment): ?>
      <div class="col-md-4">
        <div class="card h-100">
          <div class="card-header fw-bold">
            Payment
          </div>
          <div class="card-body">
            <div class="text-center mb-3">
              <img
                src="<?= esc($payment['proof']) ?>"
                class="img-thumbnail"
                style="max-height:120px"
                alt="payment proof">
            </div>

            <dl class="row mb-0 small">
              <dt class="col-5 text-muted">Amount</dt>
              <dd class="col-7 fw-semibold">
                Rp <?= number_format($payment['amount']) ?>
              </dd>

              <dt class="col-5 text-muted">Method</dt>
              <dd class="col-7"><?= $payment['method_name'] ?></dd>

              <dt class="col-5 text-muted">Paid At</dt>
              <dd class="col-7"><?= $payment['paid_at'] ?></dd>
            </dl>
          </div>
        </div>
      </div>
    <?php endif ?>

    <div class="col-md-4">
      <div class="card h-100">
        <div class="card-header fw-bold">
          Refund Account
        </div>
        <div class="card-body">
          <?php if (!empty($refund['account_name'])): ?>
            <dl class="row mb-0 small">
              <dt class="col-5 text-muted">Account Name</dt>
              <dd class="col-7 fw-semibold"><?= esc($refund['account_name']) ?></dd>

              <dt class="col-5 text-muted">Bank</dt>
              <dd class="col-7"><?= esc($refund['bank_name'] ?? '-') ?></dd>

              <dt class="col-5 text-muted">Account No</dt>
              <dd class="col-7"><?= esc($refund['account_number'] ?? '-') ?></dd>
            </dl>
          <?php else: ?>
            <p class="text-muted mb-0">No refund account</p>
          <?php endif ?>
        </div>
      </div>
    </div>

  </div>

  <div class="card mb-4">
    <div class="card-body">
      <strong>Refund Type & Items</strong>
      <div class="mt-3">
        <p class="mb-2"><strong>Type:</strong> <?= ucfirst($refund['refund_type'] ?? 'full') ?> Refund</p>

        <?php if ($refund['refund_type'] === 'partial' && !empty($refundItems)): ?>
          <div class="table-responsive">
            <table class="table table-sm mt-2 mb-0">
              <thead class="thead-light">
                <tr>
                  <th>#</th>
                  <th>Product</th>
                  <th class="text-center">Qty Refunded</th>
                  <th class="text-end">Price</th>
                  <th class="text-end">Subtotal Refund</th>
                </tr>
              </thead>
              <tbody>
                <?php $no = 1;
                foreach ($refundItems as $item): ?>
                  <tr>
                    <td><?= $no++ ?></td>
                    <td>
                      <div class="d-flex flex-column">
                        <strong><?= esc($item['product_name']) ?></strong>
                        <?php if (!empty($item['variant_name'])): ?>
                          <small class="text-muted"><?= esc($item['variant_name']) ?></small>
                        <?php endif ?>
                      </div>
                    </td>
                    <td class="text-center"><?= $item['qty_refunded'] ?></td>
                    <td class="text-end">Rp <?= number_format($item['price_per_item']) ?></td>
                    <td class="text-end">Rp <?= number_format($item['subtotal_refunded']) ?></td>
                  </tr>
                <?php endforeach ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p class="small text-muted">Full refund untuk semua items dalam order</p>
        <?php endif ?>
      </div>
    </div>
  </div>


  <div class="card-body">
    <h5 class="mb-3">Refund Details & Admin Actions</h5>

    <div class="row mb-3">
      <div class="col-md-4">
        <div class="card">
          <div class="card-body small">
            <p class="mb-1 text-muted">Refund ID</p>
            <p class="fw-semibold"><?= esc($refund['order_refund_id']) ?></p>

            <p class="mb-1 text-muted">Type</p>
            <p class="fw-semibold"><?= ucfirst($refund['type']) ?></p>

            <p class="mb-1 text-muted">Amount</p>
            <p class="fw-semibold">Rp <?= number_format($refund['refund_amount'] ?? 0) ?></p>

            <p class="mb-1 text-muted">Reason</p>
            <p class="small text-muted"><?= esc($refund['reason'] ?? '-') ?></p>

            <p class="mb-1 text-muted">Status</p>
            <p><span class="badge <?= ($refund['status'] === 'pending') ? 'bg-warning' : (($refund['status'] === 'processing') ? 'bg-primary' : (($refund['status'] === 'approved') ? 'bg-success' : 'bg-danger')) ?>"><?= strtoupper($refund['status']) ?></span></p>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <div class="card">
          <div class="card-body">
            <h6>Admin Note</h6>
            <p class="small text-muted"><?= esc($refund['admin_note'] ?? '-') ?></p>

            <?php if (!empty($refund['processed_by'])): ?>
              <p class="small text-muted">Processed by: <?= esc($refund['admin_name'] ?? $refund['admin_email'] ?? '-') ?></p>
            <?php endif ?>
          </div>
        </div>
      </div>
    </div>

    <?php if (in_array($refund['status'], ['requested', 'processing'])): ?>
      <div class="row g-3">
        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <h6>Approve Refund</h6>
              <div class="mb-2">
                <label class="form-label">Adjusted Amount (optional)</label>
                <input id="approve_amount" type="number" class="form-control" placeholder="Leave empty to keep requested amount">
              </div>
              <div class="mb-2">
                <label class="form-label">Admin Note (optional)</label>
                <textarea id="approve_note" class="form-control" rows="2"></textarea>
              </div>
              <button id="btnApprove" class="btn btn-success">Approve Refund</button>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="card">
            <div class="card-body">
              <h6>Reject Refund</h6>
              <div class="mb-2">
                <label class="form-label">Admin Note (required)</label>
                <textarea id="reject_note" class="form-control" rows="2" required></textarea>
              </div>
              <button id="btnReject" class="btn btn-danger">Reject Refund</button>
            </div>
          </div>
        </div>
      </div>
    <?php else: ?>
      <div class="alert alert-secondary">No actions available for this refund</div>
    <?php endif ?>
  </div>
</div>
</div>
<?= $this->endSection() ?>

<script>
  const refundId = '<?= esc($refund['order_refund_id']) ?>';

  async function postJson(url, body) {
    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(body)
    });
    return res.json();
  }

  document.getElementById('btnApprove')?.addEventListener('click', async () => {
    if (!confirm('Approve this refund?')) return;
    const adjusted = document.getElementById('approve_amount').value || null;
    const note = document.getElementById('approve_note').value || null;
    const url = `<?= base_url('/api/admin/refund/') ?>${refundId}/approve`;
    const payload = {};
    if (adjusted) payload.adjusted_amount = parseFloat(adjusted);
    if (note) payload.admin_note = note;
    const resp = await postJson(url, payload);
    alert(resp.message || JSON.stringify(resp));
    if (resp.success !== false) location.reload();
  });

  document.getElementById('btnReject')?.addEventListener('click', async () => {
    if (!confirm('Reject this refund?')) return;
    const note = document.getElementById('reject_note').value || '';
    if (!note) return alert('Admin note is required for rejection');
    const url = `<?= base_url('/api/admin/refund/') ?>${refundId}/reject`;
    const resp = await postJson(url, {
      admin_note: note
    });
    alert(resp.message || JSON.stringify(resp));
    if (resp.success !== false) location.reload();
  });
</script>