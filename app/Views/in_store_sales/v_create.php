<?= $this->extend('layouts/l_dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid card mt-4">
  <div class="card-header pb-0">
    <h4>Transaksi Penjualan Toko</h4>
  </div>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
      <?= session()->getFlashdata('error') ?>
    </div>
  <?php endif; ?>

  <div class="card-body">
    <form action="<?= site_url('in-store-sales/store') ?>" method="post">
      <?= csrf_field() ?>

      <!-- CUSTOMER -->
      <div class="mb-3">
        <label class="form-label">Customer</label>
        <select name="customer_id" class="form-select" required>
          <option value="">-- Pilih Customer --</option>
          <?php foreach ($customers as $customer): ?>
            <option value="<?= $customer['customer_id'] ?>">
              <?= $customer['customer_name'] ?>
            </option>
          <?php endforeach ?>
        </select>
      </div>

      <!-- ITEMS -->
      <h5 class="mt-4">Produk Dibeli</h5>

      <table class="table table-bordered" id="itemsTable">
        <thead>
          <tr>
            <th>Produk</th>
            <th>Variant</th>
            <th width="120">Harga</th>
            <th width="80">Qty</th>
            <th width="120">Subtotal</th>
            <th width="50"></th>
          </tr>
        </thead>
        <tbody>

          <tr>
            <!-- PRODUCT -->
            <td>
              <select name="items[0][product_id]"
                class="form-select product-select"
                required>
                <option value="">-- Pilih Produk --</option>
                <?php foreach ($products as $p): ?>
                  <option value="<?= $p['product_id'] ?>"
                    data-price="<?= $p['product_price'] ?>">
                    <?= $p['product_name'] ?>
                  </option>
                <?php endforeach ?>
              </select>
            </td>

            <!-- VARIANT -->
            <td>
              <select name="items[0][variant_id]"
                class="form-select variant-select"
                disabled>
                <option value="">-- Pilih Variant --</option>
              </select>
            </td>

            <!-- PRICE -->
            <td>
              <input type="number" name="items[0][price]"
                class="form-control price" readonly>
            </td>

            <!-- QTY -->
            <td>
              <input type="number" name="items[0][qty]"
                class="form-control qty" value="1" min="1">
            </td>

            <!-- SUBTOTAL -->
            <td>
              <input type="text" class="form-control subtotal" readonly>
            </td>


            <td>
              <button type="button" class="btn btn-danger btn-sm remove-row">✕</button>
            </td>
          </tr>
        </tbody>
      </table>


      <button type="button" class="btn btn-secondary btn-sm" id="addRow">
        + Tambah Produk
      </button>

      <!-- LENS / PRESCRIPTION -->
      <div class="card mt-4">
        <div class="card-body">
          <strong>Prescription</strong>

          <!-- TYPE -->
          <div class="my-3">
            <label class="form-label fw-bold">Tipe Resep</label>
            <div class="d-flex gap-4">
              <div class="form-check">
                <input class="form-check-input"
                  type="radio"
                  name="prescription[type]"
                  value="none"
                  id="rx_none"
                  checked>
                <label class="form-check-label" for="rx_none">
                  Tanpa Resep
                </label>
              </div>

              <div class="form-check">
                <input class="form-check-input"
                  type="radio"
                  name="prescription[type]"
                  value="manual"
                  id="rx_manual">
                <label class="form-check-label" for="rx_manual">
                  Input Manual
                </label>
              </div>
            </div>
          </div>

          <!-- MANUAL FORM -->
          <div id="prescription_manual" style="display:none">

            <!-- OD -->
            <h6 class="fw-bold mt-3">OD (Mata Kanan)</h6>
            <div class="row g-2">
              <div class="col">
                <input type="text" class="form-control"
                  name="prescription[right][sph]"
                  placeholder="SPH">
              </div>
              <div class="col">
                <input type="text" class="form-control"
                  name="prescription[right][cyl]"
                  placeholder="CYL">
              </div>
              <div class="col">
                <input type="text" class="form-control"
                  name="prescription[right][axis]"
                  placeholder="Axis">
              </div>
              <div class="col">
                <input type="text" class="form-control"
                  name="prescription[right][pd]"
                  placeholder="PD">
              </div>
            </div>

            <!-- OS -->
            <h6 class="fw-bold mt-3">OS (Mata Kiri)</h6>
            <div class="row g-2">
              <div class="col">
                <input type="text" class="form-control"
                  name="prescription[left][sph]"
                  placeholder="SPH">
              </div>
              <div class="col">
                <input type="text" class="form-control"
                  name="prescription[left][cyl]"
                  placeholder="CYL">
              </div>
              <div class="col">
                <input type="text" class="form-control"
                  name="prescription[left][axis]"
                  placeholder="Axis">
              </div>
              <div class="col">
                <input type="text" class="form-control"
                  name="prescription[left][pd]"
                  placeholder="PD">
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- TOTAL -->
      <div class="mt-4 text-end">
        <h4>Total: <span id="grandTotal">0</span></h4>
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary">
          Simpan Transaksi
        </button>
        <a href="<?= site_url('in_store_sales/v_index') ?>" class="btn btn-secondary">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>

<script>
  let index = 1;

  function loadVariants(productId, row) {
    const variantSelect = row.querySelector('.variant-select');
    const priceInput = row.querySelector('.price');

    const productPrice =
      row.querySelector('.product-select')
      .selectedOptions[0]
      ?.dataset.price || 0;

    fetch('<?= base_url('api/variants?productId=') ?>' + productId)
      .then(res => res.json())
      .then(({
        data
      }) => {

        variantSelect.innerHTML = '<option value="">-- Pilih Variant --</option>';

        // TIDAK ADA VARIANT
        if (!data || data.length === 0) {
          variantSelect.disabled = true;
          priceInput.value = productPrice;
          updateSubtotal(row);
          return;
        }

        // ADA VARIANT
        data.forEach(v => {
          const opt = document.createElement('option');
          opt.value = v.variant_id;
          opt.textContent = v.variant_name;
          opt.dataset.price = v.price;
          variantSelect.appendChild(opt);
        });

        variantSelect.disabled = false;

        // ⬅️ DEFAULT KEMBALI KE PRODUCT PRICE
        priceInput.value = productPrice;
        updateSubtotal(row);
      });
  }

  document.addEventListener('change', function(e) {
    if (e.target.classList.contains('variant-select')) {
      const row = e.target.closest('tr');
      const priceInput = row.querySelector('.price');

      const productPrice =
        row.querySelector('.product-select')
        .selectedOptions[0]
        ?.dataset.price || 0;

      const selectedOption = e.target.selectedOptions[0];

      // ⬅️ JIKA variant dikosongkan
      if (!selectedOption || !selectedOption.value) {
        priceInput.value = productPrice;
        updateSubtotal(row);
        return;
      }

      // ⬅️ JIKA variant dipilih
      priceInput.value = selectedOption.dataset.price || productPrice;
      updateSubtotal(row);
    }
  });

  document.querySelectorAll('input[name="prescription[type]"]').forEach(radio => {
    radio.addEventListener('change', function() {
      const manualForm = document.getElementById('prescription_manual');

      if (this.value === 'manual') {
        manualForm.style.display = 'block';
      } else {
        manualForm.style.display = 'none';

        // reset field jika pilih "tanpa resep"
        manualForm.querySelectorAll('input').forEach(input => {
          input.value = '';
        });
      }
    });
  });

  function updateSubtotal(row) {
    const price = Number(row.querySelector('.price').value || 0);
    const qty = Number(row.querySelector('.qty').value || 1);
    row.querySelector('.subtotal').value = price * qty;
    updateTotal();
  }

  function updateTotal() {
    let total = 0;
    document.querySelectorAll('.subtotal').forEach(el => {
      total += Number(el.value || 0);
    });
    document.getElementById('grandTotal').innerText =
      total.toLocaleString('id-ID');
  }

  /* EVENT HANDLER */
  document.addEventListener('change', function(e) {
    const row = e.target.closest('tr');

    // PRODUCT CHANGE
    if (e.target.classList.contains('product-select')) {
      const price = e.target.selectedOptions[0]?.dataset.price || 0;
      row.querySelector('.price').value = price;
      row.querySelector('.qty').value = 1;
      loadVariants(e.target.value, row);
      updateSubtotal(row);
    }

    // QTY CHANGE
    if (e.target.classList.contains('qty')) {
      updateSubtotal(row);
    }
  });

  /* ADD ROW */
  document.getElementById('addRow').addEventListener('click', function() {
    const tbody = document.querySelector('#itemsTable tbody');
    const newRow = tbody.rows[0].cloneNode(true);

    newRow.querySelectorAll('select, input').forEach(el => {
      el.name = el.name.replace(/\[\d+\]/, `[${index}]`);
      el.value = '';
    });

    newRow.querySelector('.variant-select').disabled = true;

    tbody.appendChild(newRow);
    index++;
  });

  /* REMOVE ROW */
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-row')) {
      const tbody = document.querySelector('#itemsTable tbody');
      if (tbody.rows.length > 1) {
        e.target.closest('tr').remove();
        updateTotal();
      }
    }
  });
</script>


<?= $this->endSection() ?>