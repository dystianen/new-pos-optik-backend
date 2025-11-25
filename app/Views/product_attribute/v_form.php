<?= $this->extend('layouts/l_dashboard.php') ?>
<?= $this->section('content') ?>
<div class="container-fluid card">
  <div class="card-header pb-0">
    <h4><?= isset($attribute) ? 'Edit' : 'Add' ?> Product Attribute</h4>
  </div>
  <div class="card-body">
    <form action="<?= site_url('/product-attribute/save') ?>" method="post">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= isset($attribute) ? $attribute['attribute_id'] : '' ?>">

      <div class="mb-3">
        <label for="attribute_name" class="form-label">Attribute Name</label>
        <input
          type="text"
          name="attribute_name"
          class="form-control"
          value="<?= isset($attribute) ? esc($attribute['attribute_name']) : '' ?>"
          required>
      </div>
      <div class="mb-3">
        <label for="attribute_type">Attribute Type</label>
        <select class="form-control" name="attribute_type" id="attribute_type">
          <option value="text" <?= isset($attribute) && $attribute['attribute_type'] == 'text' ? 'selected' : '' ?>>Text</option>
          <option value="number" <?= isset($attribute) && $attribute['attribute_type'] == 'number' ? 'selected' : '' ?>>Number</option>
          <option value="dropdown" <?= isset($attribute) && $attribute['attribute_type'] == 'dropdown' ? 'selected' : '' ?>>Dropdown</option>
        </select>
      </div>
      <a href="<?= base_url('/product-attribute') ?>" class="btn btn-secondary">Cancel</a>
      <button type="submit" class="btn btn-primary"><?= isset($attribute) ? 'Update' : 'Save' ?></button>
    </form>
  </div>
</div>
<?= $this->endSection() ?>