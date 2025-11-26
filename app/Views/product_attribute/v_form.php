<?= $this->extend('layouts/l_dashboard.php') ?>
<?= $this->section('content') ?>

<div class="container-fluid card">
  <div class="card-header pb-0">
    <h4><?= isset($attribute) ? 'Edit' : 'Add' ?> Product Attribute</h4>
  </div>

  <div class="card-body">
    <form action="<?= site_url('/product-attribute/save') ?>" method="post">
      <?= csrf_field() ?>

      <input type="hidden" name="id" value="<?= $attribute['attribute_id'] ?? '' ?>">

      <div class="mb-3">
        <label class="form-label">Attribute Name</label>
        <input type="text" name="attribute_name" class="form-control"
          value="<?= esc($attribute['attribute_name'] ?? '') ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Attribute Type</label>
        <select class="form-control" name="attribute_type" id="attribute_type">
          <option value="text" <?= isset($attribute) && $attribute['attribute_type'] === 'text' ? 'selected' : '' ?>>Text</option>
          <option value="number" <?= isset($attribute) && $attribute['attribute_type'] === 'number' ? 'selected' : '' ?>>Number</option>
          <option value="dropdown" <?= isset($attribute) && $attribute['attribute_type'] === 'dropdown' ? 'selected' : '' ?>>Dropdown</option>
        </select>
      </div>

      <!-- dynamic dropdown values -->
      <div id="dropdown-values" class="mb-3" style="display: none;">
        <label class="form-label">Dropdown Options</label>

        <div id="value-list">
          <?php if (isset($options)): ?>
            <?php foreach ($options as $opt): ?>
              <div class="dropdown-row mb-2 d-flex align-items-center" style="gap: 10px;">
                <input type="hidden" name="value_ids[]" value="<?= $opt['attribute_master_id'] ?>">
                <input type="text" name="values[]" class="form-control" value="<?= esc($opt['value']) ?>">
                <button type="button" class="btn btn-danger remove-value">
                  <i class="fas fa-trash"></i>
                </button>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <button type="button" id="add-value" class="btn btn-secondary btn-sm">+ Add Value</button>
      </div>

      <a href="<?= base_url('/product-attribute') ?>" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary"><?= isset($attribute) ? 'Update' : 'Save' ?></button>
    </form>
  </div>
</div>

<script>
  function refreshDropdownVisibility() {
    const type = document.getElementById("attribute_type").value;
    document.getElementById("dropdown-values").style.display = (type === "dropdown") ? "block" : "none";
  }

  document.getElementById("attribute_type").addEventListener("change", refreshDropdownVisibility);
  refreshDropdownVisibility();

  document.getElementById("add-value").addEventListener("click", function() {
    const div = document.createElement("div");
    div.classList.add("dropdown-row", "mb-2", "d-flex", "align-items-center");
    div.style.gap = "10px";

    div.innerHTML = `
        <input type="hidden" name="value_ids[]" value="">
        <input type="text" name="values[]" class="form-control">
        <button type="button" class="btn btn-danger remove-value">
            <i class="fas fa-trash"></i>
        </button>
    `;

    document.getElementById("value-list").appendChild(div);
  });


  document.addEventListener("click", function(e) {
    const delBtn = e.target.closest(".remove-value");
    if (delBtn) {
      delBtn.closest(".dropdown-row").remove();
    }
  });
</script>

<?= $this->endSection() ?>