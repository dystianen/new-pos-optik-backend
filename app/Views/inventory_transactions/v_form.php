<?= $this->extend('layouts/l_dashboard') ?>
<?= $this->section('content') ?>
<div class="container-fluid card">
  <div class="card-header pb-0">
    <h4><?= isset($transaction) ? 'Edit Inventory Transaction' : 'Create Inventory Transaction' ?></h4>
  </div>

  <div class="card-body">
    <form action="<?= site_url('inventory/save') ?>" method="post">
      <input type="hidden" name="id" value="<?= isset($transaction) ? $transaction['inventory_transaction_id'] : '' ?>">

      <!-- ✅ HIDDEN FIELDS UNTUK EDIT MODE -->
      <?php if (isset($transaction)): ?>
        <input type="hidden" name="product_id" value="<?= $transaction['product_id'] ?>">
        <input type="hidden" name="variant_id" value="<?= $transaction['variant_id'] ?>">
      <?php endif; ?>

      <div class="row">

        <!-- PRODUCT -->
        <div class="col-12 col-md-6 mb-3">
          <label for="product_id">Product</label>
          <select
            <?= isset($transaction) ? "disabled" : "" ?>
            class="form-control"
            <?= !isset($transaction) ? 'name="product_id"' : '' ?>
            id="product_id"
            <?= !isset($transaction) ? 'required' : '' ?>>
            <option value="">-- Select Product --</option>
            <?php foreach ($products as $product) : ?>
              <option value="<?= $product['product_id'] ?>"
                <?= isset($transaction) && $transaction['product_id'] == $product['product_id'] ? 'selected' : '' ?>>
                <?= $product['product_name'] ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- VARIANT -->
        <div class="col-12 col-md-6 mb-3" id="variant_wrapper" style="display:none;">
          <label for="variant_id">Variant</label>
          <select
            <?= isset($transaction) ? "disabled" : "" ?>
            class="form-control"
            <?= !isset($transaction) ? 'name="variant_id"' : '' ?>
            id="variant_id">
            <option value="">-- Select Variant --</option>
          </select>
        </div>

        <!-- TYPE -->
        <div class="col-12 col-md-6 mb-3">
          <label for="transaction_type">Transaction Type</label>
          <select class="form-control" name="transaction_type" id="transaction_type" required>
            <option value="in" <?= isset($transaction) && $transaction['transaction_type'] == 'in' ? 'selected' : '' ?>>IN</option>
            <option value="out" <?= isset($transaction) && $transaction['transaction_type'] == 'out' ? 'selected' : '' ?>>OUT</option>
          </select>
        </div>

        <!-- QTY -->
        <div class="col-12 col-md-6 mb-3">
          <label for="quantity">Quantity</label>
          <input class="form-control" type="number" placeholder="0" name="quantity" id="quantity" required min="1"
            value="<?= isset($transaction) ? $transaction['quantity'] : '' ?>">
        </div>

        <!-- DESCRIPTION -->
        <div class="col-12 col-md-6 mb-3">
          <label for="description">Description</label>
          <textarea class="form-control" name="description" id="description" placeholder="Notes..."><?= isset($transaction) ? $transaction['description'] : '' ?></textarea>
        </div>

        <div class="mt-4">
          <a href="<?= base_url('/inventory') ?>" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary"><?= isset($transaction) ? 'Update' : 'Save' ?></button>
        </div>

      </div>
    </form>
  </div>
</div>


<!-- AJAX SCRIPT -->
<script>
  document.addEventListener("DOMContentLoaded", function() {

    function loadVariants(productId, selectedVariant = null) {
      if (!productId) {
        document.getElementById("variant_wrapper").style.display = "none";
        document.getElementById("variant_id").innerHTML = "<option value=''>-- Select Variant --</option>";
        return;
      }

      fetch('<?= base_url('api/variants?productId=') ?>' + productId)
        .then(response => response.json())
        .then(({
          data
        }) => {
          let variantSelect = document.getElementById("variant_id");
          variantSelect.innerHTML = "";

          if (data.length === 0) {
            document.getElementById("variant_wrapper").style.display = "none";
            return;
          }

          // Show Variant field
          document.getElementById("variant_wrapper").style.display = "block";

          variantSelect.innerHTML = "<option value=''>-- Select Variant --</option>";

          data.forEach(v => {
            let option = document.createElement("option");
            option.value = v.variant_id;
            option.textContent = v.variant_name;

            if (selectedVariant && selectedVariant == v.variant_id) {
              option.selected = true;
            }

            variantSelect.appendChild(option);
          });
        })
        .catch(err => console.error("Failed to load variants", err));
    }

    // Initial load (for edit form)
    let initialProduct = document.getElementById("product_id").value;
    let initialVariant = "<?= isset($transaction) ? $transaction['variant_id'] ?? '' : '' ?>";

    if (initialProduct) {
      loadVariants(initialProduct, initialVariant);
    }

    // When product changes (only for create mode)
    <?php if (!isset($transaction)): ?>
      document.getElementById("product_id").addEventListener("change", function() {
        loadVariants(this.value, null);
      });
    <?php endif; ?>
  });
</script>

<?= $this->endSection() ?>