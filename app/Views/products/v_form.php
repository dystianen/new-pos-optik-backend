<?= $this->extend('layouts/l_dashboard') ?>
<?= $this->section('content') ?>

<div class="container-fluid card">
  <div class="card-header pb-0">
    <h4><?= isset($product) ? 'Edit Product' : 'Create Product' ?></h4>
  </div>

  <div class="card-body">
    <form id="productForm" action="<?= site_url('products/save') ?>" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>

      <input type="hidden" name="id" value="<?= $product['product_id'] ?? '' ?>">

      <div class="row">
        <!-- Category -->
        <div class="col-12 col-md-6 mb-3">
          <label class="form-label">Category</label>
          <select class="form-control" name="category_id" required>
            <option value="" disabled <?= !isset($product) ? 'selected' : '' ?>>Select category</option>
            <?php foreach ($categories as $c): ?>
              <option value="<?= $c['category_id'] ?>"
                <?= (old('category_id', $product['category_id'] ?? '') == $c['category_id']) ? 'selected' : '' ?>>
                <?= esc($c['category_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Name -->
        <div class="col-12 col-md-6 mb-3">
          <label class="form-label">Name</label>
          <input type="text" name="product_name" class="form-control"
            value="<?= old('product_name', $product['product_name'] ?? '') ?>" required>
        </div>

        <!-- Price (base) -->
        <div class="col-12 col-md-6 mb-3">
          <label class="form-label">Base Price</label>
          <input type="number" step="0.01" name="product_price" class="form-control"
            value="<?= old('product_price', $product['product_price'] ?? '') ?>" required>
          <small class="text-muted">This is the default price (used if variant price not provided).</small>
        </div>

        <!-- Stock (base) -->
        <div class="col-12 col-md-6 mb-3">
          <label class="form-label">Base Stock</label>
          <input type="number" name="product_stock" class="form-control"
            value="<?= old('product_stock', $product['product_stock'] ?? '') ?>" required>
          <small class="text-muted">Used if variants are not enabled or variant stock is empty.</small>
        </div>

        <!-- Brand -->
        <div class="col-12 col-md-6 mb-3">
          <label class="form-label">Brand</label>
          <input type="text" name="product_brand" class="form-control"
            value="<?= old('product_brand', $product['product_brand'] ?? '') ?>" required>
        </div>

        <!-- MULTIPLE IMAGES -->
        <div class="col-12 mb-3">
          <label class="form-label">Product Images</label>
          <input type="file" name="images[]" class="form-control" multiple accept=".jpg,.jpeg,.png">
          <small class="text-muted">You can upload multiple images. These are used as product images and fallback for variants.</small>

          <?php if (!empty($product_images)): ?>
            <div class="mt-2">
              <label>Current Images:</label>
              <div class="d-flex flex-wrap">
                <?php foreach ($product_images as $img): ?>
                  <div class="me-2 mb-2 text-center">
                    <img src="<?= base_url('uploads/products/' . $img['url']) ?>" width="80" class="rounded border"><br>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <!-- DYNAMIC ATTRIBUTES -->
        <div class="col-12 mt-4">
          <h5>Product Attributes</h5>
          <p class="text-muted">Fill attribute values. To use an attribute as variant option, toggle "Use as variant".</p>
          <div class="row">

            <?php foreach ($attributes as $attr): ?>
              <?php
              $val = $productAttributeValues[$attr['attribute_id']] ?? '';
              // store previous choice whether this attribute is used as variant
              $isVariant = in_array($attr['attribute_id'], $variantAttributes ?? []) ? 'checked' : '';
              ?>

              <div class="col-12 col-md-6 mb-3">
                <label class="form-label"><?php echo esc($attr['attribute_name']) ?></label>
                <div class="input-group">
                  <input type="text" class="form-control"
                    name="attributes[<?= $attr['attribute_id'] ?>]"
                    placeholder="Enter <?= strtolower($attr['attribute_name']) ?> (comma separated for multiple)"
                    value="<?= esc($val) ?>">
                  <div class="input-group-text">
                    <label class="mb-0">
                      <input type="checkbox" name="variant_attributes[]" value="<?= $attr['attribute_id'] ?>" <?= $isVariant ?>> Use as variant
                    </label>
                  </div>
                </div>
                <small class="text-muted">For example: Red,Blue,Black</small>
              </div>

            <?php endforeach; ?>

          </div>
        </div>

        <!-- VARIANT SECTION -->
        <div class="col-12 mt-3" id="variantSection" style="display: none;">
          <h5>Variant List</h5>
          <p class="text-muted">Automated combination of selected variant attributes. You can edit price, stock and image per variant.</p>

          <div class="table-responsive">
            <table class="table table-bordered" id="variantTable">
              <thead>
                <tr>
                  <th>Variant</th>
                  <th>Price</th>
                  <th>Stock</th>
                  <th>Image</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

          <button type="button" id="rebuildVariants" class="btn btn-sm btn-secondary mt-2">Regenerate Variants</button>
        </div>

      </div>

      <div class="mt-4 d-flex gap-2">
        <a href="<?= base_url('/products') ?>" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">
          <?= isset($product) ? 'Update' : 'Save' ?>
        </button>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  // Simple JS to manage variant generation & UI. Uses vanilla JS so it works without additional libs.
  (function() {
    const form = document.getElementById('productForm');
    const variantSection = document.getElementById('variantSection');
    const variantTableBody = document.querySelector('#variantTable tbody');
    const rebuildBtn = document.getElementById('rebuildVariants');

    // REHYDRATE VARIANTS FROM PHP
    const existingVariants = <?= isset($variants) ? json_encode($variants) : '[]' ?>;
    const pavValues = <?= isset($pav_values) ? json_encode($pav_values) : '[]' ?>;
    console.log({
      existingVariants
    })

    function getVariantAttributes() {
      const checked = Array.from(document.querySelectorAll('input[name="variant_attributes[]"]:checked'));
      // For each attribute id, read the corresponding attributes[...] input and split by comma
      return checked.map(cb => {
        const attrId = cb.value;
        const input = document.querySelector('input[name="attributes[' + attrId + ']"]');
        const raw = input ? input.value : '';
        const vals = raw.split(',').map(s => s.trim()).filter(Boolean);
        const name = input.previousElementSibling ? input.previousElementSibling.textContent : '';
        return {
          id: attrId,
          name: attrId,
          values: vals
        };
      });
    }

    function generateCombinations(arrays) {
      if (!arrays.length) return [];
      return arrays.reduce((acc, curr) => {
        const res = [];
        acc.forEach(a => {
          curr.values.forEach(v => res.push(a.concat([{
            attrId: curr.id,
            value: v
          }])));
        });
        return res;
      }, [
        []
      ]);
    }

    function renderVariants() {
      const attrs = getVariantAttributes();
      if (!attrs.length) {
        variantSection.style.display = 'none';
        variantTableBody.innerHTML = '';
        return;
      }

      // ensure each selected attribute has at least one value
      for (const a of attrs) {
        if (!a.values.length) {
          alert('Attribute with id ' + a.id + ' has no values. Add comma-separated values to attribute input.');
          return;
        }
      }

      const combos = generateCombinations(attrs);
      variantSection.style.display = combos.length ? 'block' : 'none';
      variantTableBody.innerHTML = '';

      combos.forEach((combo, idx) => {
        const variantLabel = combo.map(c => c.value).join(' - ');
        const sku = 'VAR-' + idx + '-' + Math.random().toString(36).slice(2, 7).toUpperCase();

        const tr = document.createElement('tr');
        tr.innerHTML = `
        <td>${variantLabel}
          <input type="hidden" name="variants[${idx}][label]" value="${escapeHtml(variantLabel)}">
        </td>
        <td><input type="number" step="0.01" name="variants[${idx}][price]" class="form-control form-control-sm" placeholder="Leave empty to use base price"></td>
        <td><input type="number" name="variants[${idx}][stock]" class="form-control form-control-sm" placeholder="Leave empty to use base stock"></td>
        <td>
          <input type="file" name="variants[${idx}][image]" accept=".jpg,.jpeg,.png" class="form-control form-control-sm">
        </td>
        <td>
          <button type="button" class="btn btn-sm btn-danger remove-variant">Remove</button>
        </td>
      `;

        // store attribute mapping to reconstruct on backend
        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = `variants[${idx}][mapping]`;
        hidden.value = JSON.stringify(combo.map(c => ({
          attribute_id: c.attrId,
          value: c.value
        })));
        tr.appendChild(hidden);

        variantTableBody.appendChild(tr);
      });

      // add remove handlers
      Array.from(document.querySelectorAll('.remove-variant')).forEach(btn => {
        btn.addEventListener('click', function() {
          this.closest('tr').remove();
        });
      });
    }

    function renderExistingVariants() {
      variantTableBody.innerHTML = '';

      existingVariants.forEach((v, idx) => {
        const tr = document.createElement('tr');

        const mappingJson = JSON.stringify(v.pav_mapping || []);

        tr.innerHTML = `
          <td>
            ${v.variant_name}
            <input type="hidden" name="variants[${idx}][label]" value="${escapeHtml(v.variant_name)}">
            <input type="hidden" name="variants[${idx}][variant_id]" value="${v.variant_id}">
          </td>

          <td>
            <input type="number" step="0.01" name="variants[${idx}][price]" class="form-control form-control-sm"
              value="${v.price || ''}">
          </td>

          <td>
            <input type="number" name="variants[${idx}][stock]" class="form-control form-control-sm"
              value="${v.stock || ''}">
          </td>

          <td>
            <img src="/uploads/products/${v.variant_image.url}" width="50" height="50">
            <input type="file" name="variants[${idx}][image]" accept=".jpg,.jpeg,.png" class="form-control form-control-sm mt-1">
          </td>

          <td>
            <button type="button" class="btn btn-sm btn-danger remove-variant">Remove</button>
          </td>
        `;

        const hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.name = `variants[${idx}][mapping]`;
        hidden.value = mappingJson;
        tr.appendChild(hidden);

        variantTableBody.appendChild(tr);
      });
    }

    function escapeHtml(unsafe) {
      return unsafe.replace(/[&<"'>]/g, function(m) {
        return ({
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          '"': '&quot',
          "'": '&#039'
        })[m];
      });
    }

    // events
    document.addEventListener('change', function(e) {
      if (e.target.matches('input[name^="attributes["]') || e.target.matches('input[name="variant_attributes[]"]')) {
        // small debounce
        clearTimeout(window._variantTimer);
        window._variantTimer = setTimeout(renderVariants, 300);
      }
    });

    rebuildBtn.addEventListener('click', renderVariants);

    // initial render if editing product with variants
    window.addEventListener('load', function() {
      if (existingVariants.length > 0) {
        renderExistingVariants();
        variantSection.style.display = 'block';
      } else {
        renderVariants();
      }
    });

  })();
</script>
<?= $this->endSection() ?>