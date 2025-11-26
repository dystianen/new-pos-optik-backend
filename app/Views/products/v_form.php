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
                  <div class="me-2 mb-2 position-relative image-container" style="width: 150px;">
                    <img src="<?= esc($img['url']) ?>" class="rounded border w-100 h-100" style="object-fit: contain;" alt="<?= esc($img['alt_text']) ?>">

                    <!-- Overlay dengan button delete di tengah -->
                    <div class="image-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                      <button
                        type="button"
                        class="btn btn-danger btn-sm delete-image-btn"
                        data-image-id="<?= $img['product_image_id'] ?>"
                        data-product-id="<?= $product['product_id'] ?>"
                        title="Delete Image">
                        <i class="fas fa-trash"></i>
                      </button>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>

        <!-- DYNAMIC ATTRIBUTES -->
        <div class="col-12 mt-4">
          <h5>Product Attributes</h5>
          <p class="text-muted">Fill in the attributes and select which one you want to make a variant.</p>

          <div class="row g-4">

            <?php
            // Daftar attribute yang boleh jadi variant
            $variantAllowed = [
              'Color',
              'Lens Type',
              'Frame Size (Width)',
              'Bridge Size',
              'Temple Length',
            ];
            ?>

            <?php foreach ($attributes as $attr): ?>

              <?php
              $attrName = $attr['attribute_name'];

              // value untuk edit
              $existingValue = $pav_values[$attr['attribute_id']]['value'] ?? '';

              // apakah attribute ini dipilih sebagai variant
              $isVariant = in_array($attr['attribute_id'], $selected_attributes ?? []) ? 'checked' : '';

              // selected checkbox value
              $selectedValues = $selected_attribute_values[$attr['attribute_id']] ?? [];

              // cek apakah attribute boleh jadi variant
              $allowed = in_array($attrName, $variantAllowed);
              ?>

              <div class="col-12 col-md-6">
                <div class="p-3 border rounded-3 h-100">

                  <!-- NAMA ATTRIBUTE + TOGGLE VARIANT -->
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="fw-bold mb-1"><?= esc($attrName) ?></label>

                    <?php if ($allowed): ?>
                      <div class="form-check form-switch">
                        <input
                          class="form-check-input"
                          type="checkbox"
                          name="variant_attributes[]"
                          value="<?= $attr['attribute_id'] ?>"
                          <?= $isVariant ?>>
                        <label class="form-check-label">Variant</label>
                      </div>
                    <?php endif; ?>

                  </div>

                  <!-- JIKA TIPE TEXT -->
                  <?php if ($attr['attribute_type'] === 'text'): ?>

                    <input
                      type="text"
                      class="form-control"
                      name="attributes[<?= $attr['attribute_id'] ?>]"
                      placeholder="Enter <?= strtolower($attrName) ?>"
                      value="<?= esc($existingValue) ?>">

                  <?php endif; ?>

                  <!-- JIKA TIPE SELECT: checkbox list -->
                  <?php if ($attr['attribute_type'] === 'select'): ?>

                    <input
                      type="text"
                      class="form-control mb-2"
                      name="attributes[<?= $attr['attribute_id'] ?>]"
                      placeholder="Enter <?= strtolower($attrName) ?> (comma separated)"
                      value="<?= esc($existingValue) ?>">

                    <?php if (!empty($attr['values'])): ?>
                      <div class="mt-2">

                        <?php foreach ($attr['values'] as $val): ?>
                          <div class="form-check form-check-inline mb-2">
                            <input
                              class="form-check-input"
                              type="checkbox"
                              name="attribute_values[<?= $attr['attribute_id'] ?>][]"
                              value="<?= esc($val['value']) ?>"
                              <?= in_array($val['value'], $selectedValues) ? 'checked' : '' ?>>
                            <label class="form-check-label">
                              <?= esc($val['value']) ?>
                            </label>
                          </div>
                        <?php endforeach; ?>

                      </div>
                    <?php endif; ?>

                  <?php endif; ?>

                </div>
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

<!-- CSS -->
<style>
  .image-container {
    overflow: hidden;
  }

  .image-overlay {
    background-color: rgba(0, 0, 0, 0);
    transition: background-color 0.3s ease;
    opacity: 0;
    border-radius: 0.25rem;
  }

  .image-container:hover .image-overlay {
    background-color: rgba(0, 0, 0, 0.6);
    opacity: 1;
  }

  .delete-image-btn {
    padding: 8px 12px;
    font-size: 16px;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    transform: scale(0.8);
    transition: transform 0.2s ease;
  }

  .image-container:hover .delete-image-btn {
    transform: scale(1);
  }

  .delete-image-btn:hover {
    background-color: #c82333 !important;
    transform: scale(1.1) !important;
  }
</style>


<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-image-btn');

    deleteButtons.forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();

        const imageId = this.dataset.imageId;
        const productId = this.dataset.productId;
        const imageContainer = this.closest('.image-container');
        const btn = this;

        // SweetAlert Konfirmasi
        Swal.fire({
          title: 'Hapus Gambar?',
          text: 'Gambar yang dihapus tidak dapat dikembalikan.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Ya, hapus',
          cancelButtonText: 'Batal',
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6'
        }).then((result) => {
          if (!result.isConfirmed) return;

          // Button loading
          btn.disabled = true;
          btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

          // AJAX Request
          fetch('<?= base_url('products/delete-image') ?>', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
              },
              body: JSON.stringify({
                image_id: imageId,
                product_id: productId
              })
            })
            .then(response => response.json())
            .then(data => {

              if (data.success) {

                // Animasi fade-out
                imageContainer.style.transition = 'opacity 0.3s, transform 0.3s';
                imageContainer.style.opacity = '0';
                imageContainer.style.transform = 'scale(0.8)';

                setTimeout(() => {
                  imageContainer.remove();

                  // SweetAlert success
                  Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message || 'Gambar berhasil dihapus.',
                    timer: 1500,
                    showConfirmButton: false
                  });
                }, 300);

              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Gagal!',
                  text: data.message || 'Tidak dapat menghapus gambar.'
                });

                // Reset button
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-trash"></i>';
              }
            })
            .catch(error => {
              console.error('Error:', error);

              Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat menghapus gambar.'
              });

              btn.disabled = false;
              btn.innerHTML = '<i class="bi bi-trash"></i>';
            });
        });
      });
    });
  });


  (function() {
    const form = document.getElementById('productForm');
    const variantSection = document.getElementById('variantSection');
    const variantTableBody = document.querySelector('#variantTable tbody');
    const rebuildBtn = document.getElementById('rebuildVariants');

    // REHYDRATE VARIANTS FROM PHP
    const existingVariants = <?= isset($variants) ? json_encode($variants) : '[]' ?>;
    const pavValues = <?= isset($pav_values) ? json_encode($pav_values) : '[]' ?>;

    // TRACK NEXT INDEX untuk variant baru
    let nextVariantIndex = existingVariants.length;

    function getVariantAttributes() {
      const checked = Array.from(document.querySelectorAll('input[name="variant_attributes[]"]:checked'));
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
        // JANGAN HAPUS EXISTING VARIANTS
        // variantTableBody.innerHTML = '';
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

      // JANGAN HAPUS SEMUA, hanya hapus yang baru (tanpa variant_id)
      // variantTableBody.innerHTML = '';

      // Hapus hanya variant yang tidak punya variant_id (variant baru yang belum disave)
      const rowsToRemove = Array.from(variantTableBody.querySelectorAll('tr')).filter(tr => {
        return !tr.querySelector('input[name*="[variant_id]"]');
      });
      rowsToRemove.forEach(row => row.remove());

      // Reset index untuk variant baru
      nextVariantIndex = variantTableBody.querySelectorAll('tr').length;

      combos.forEach((combo) => {
        const variantLabel = combo.map(c => c.value).join(' - ');

        // CEK apakah variant ini sudah ada (by label atau mapping)
        const exists = Array.from(variantTableBody.querySelectorAll('tr')).some(tr => {
          const label = tr.querySelector('input[name*="[label]"]');
          return label && label.value === variantLabel;
        });

        if (exists) {
          console.log('Variant already exists:', variantLabel);
          return; // Skip jika sudah ada
        }

        const idx = nextVariantIndex++;

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

        // store attribute mapping
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
      attachRemoveHandlers();
    }

    function renderExistingVariants() {
      // JANGAN HAPUS SEMUA - hanya render yang belum ada
      // variantTableBody.innerHTML = '';

      existingVariants.forEach((v, idx) => {
        // Cek apakah variant ini sudah di-render
        const alreadyRendered = variantTableBody.querySelector(`input[name="variants[${idx}][variant_id]"][value="${v.variant_id}"]`);
        if (alreadyRendered) {
          return; // Skip jika sudah di-render
        }

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
            <input type="file" name="variants[${idx}][image]" accept=".jpg,.jpeg,.png" class="form-control form-control-sm mb-1">
            ${v.variant_image ? `<img src="${v.variant_image.url}" width="30">` : ''}
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

      attachRemoveHandlers();
      nextVariantIndex = existingVariants.length;
    }

    function attachRemoveHandlers() {
      document.querySelectorAll('.remove-variant').forEach(btn => {
        btn.removeEventListener('click', handleRemove); // Hindari double binding
        btn.addEventListener('click', handleRemove);
      });
    }

    function handleRemove(e) {
      e.target.closest('tr').remove();
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
        clearTimeout(window._variantTimer);
        window._variantTimer = setTimeout(renderVariants, 300);
      }
    });

    rebuildBtn.addEventListener('click', function() {
      // Hapus semua variant baru (tanpa variant_id)
      const rowsToRemove = Array.from(variantTableBody.querySelectorAll('tr')).filter(tr => {
        return !tr.querySelector('input[name*="[variant_id]"]');
      });
      rowsToRemove.forEach(row => row.remove());

      // Render ulang dari kombinasi
      renderVariants();
    });

    // initial render
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