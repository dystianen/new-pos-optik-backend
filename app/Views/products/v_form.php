<?= $this->extend('layouts/l_dashboard') ?>
<?= $this->section('content') ?>
<div class="container-fluid card">
  <div class="card-header pb-0">
    <h4><?= isset($product) ? 'Edit Product' : 'Create Product' ?></h4>
  </div>

  <div class="card-body">
    <form action="<?= site_url('products/save') ?>" method="post" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= isset($product) ? $product['product_id'] : '' ?>">

      <div class="row">
        <div class="col-12 col-md-6 mb-3">
          <label for="category_id" class="form-label">Category</label>
          <select class="form-control" name="category_id" required>
            <option value="" disabled <?= !isset($product) ? 'selected' : '' ?>>Select category</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?= $category['category_id']; ?>"
                <?= (old('category_id', $product['category_id'] ?? '') == $category['category_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($category['category_name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-12 col-md-6 mb-3">
          <label for="product_name" class="form-label">Name</label>
          <input type="text" class="form-control" name="product_name" placeholder="cth: Adidas Ultra Boost"
            value="<?= old('product_name', $product['product_name'] ?? '') ?>" required>
        </div>

        <div class="col-12 col-md-6 mb-3">
          <label for="product_price" class="form-label">Price</label>
          <input type="number" step="0.01" class="form-control" name="product_price" placeholder="cth: 1.500.000"
            value="<?= old('product_price', $product['product_price'] ?? '') ?>" required>
        </div>

        <div class="col-12 col-md-6 mb-3">
          <label for="product_stock" class="form-label">Stock</label>
          <input type="number" class="form-control" name="product_stock" placeholder="cth: 25"
            value="<?= old('product_stock', $product['product_stock'] ?? '') ?>" required>
        </div>

        <div class="col-12 col-md-6 mb-3">
          <label for="product_brand" class="form-label">Brand</label>
          <input type="text" class="form-control" name="product_brand" placeholder="cth: Adidas"
            value="<?= old('product_brand', $product['product_brand'] ?? '') ?>" required>
        </div>

        <div class="col-12 col-md-6 mb-3">
          <label for="product_image_url" class="form-label">Image</label>
          <input
            type="file"
            class="form-control"
            name="product_image_url"
            accept=".jpg,.png" />
          <?php if (isset($product['product_image_url'])): ?>
            <small class="form-text text-muted">Current: <?= $product['product_image_url'] ?></small>
          <?php endif; ?>
        </div>
      </div>

      <div class="mt-4">
        <a href="<?= base_url('/products') ?>" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary"><?= isset($product) ? 'Update' : 'Save' ?></button>
      </div>
    </form>
  </div>
</div>
<?= $this->endSection() ?>