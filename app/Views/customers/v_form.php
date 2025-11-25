<?= $this->extend('layouts/l_dashboard') ?>
<?= $this->section('content') ?>
<div class="container-fluid card py-4">
  <div class="card-header pb-0">
    <h4><?= isset($customer) ? 'Edit Customer' : 'Add Customer' ?></h4>
  </div>

  <div class="card-body">
    <form action="<?= site_url('customers/save') ?>" method="post">
      <input type="hidden" name="id" value="<?= isset($customer) ? $customer['customer_id'] : '' ?>">

      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="customer_name">Name</label>
          <input type="text" class="form-control" name="customer_name" id="customer_name" placeholder="e.g., Rudi Amanah" required
            value="<?= isset($customer) ? $customer['customer_name'] : '' ?>">
        </div>

        <div class="col-md-6 mb-3">
          <label for="customer_email">Email</label>
          <input type="email" class="form-control" name="customer_email" id="customer_email" placeholder="your@email.com" required
            value="<?= isset($customer) ? $customer['customer_email'] : '' ?>">
        </div>

        <?php if (!isset($customer)): ?>
          <div class="col-md-6 mb-3">
            <label for="customer_password">Password <?= isset($customer) ? '(Leave blank to keep current)' : '' ?></label>
            <input type="password" class="form-control" name="customer_password" id="customer_password" placeholder="******"
              <?= isset($customer) ? '' : 'required' ?>>
          </div>
        <?php endif; ?>

        <div class="col-md-6 mb-3">
          <label for="customer_phone">Phone</label>
          <input type="text" class="form-control" name="customer_phone" id="customer_phone" placeholder="e.g., +62 813-3948-3847"
            value="<?= isset($customer) ? $customer['customer_phone'] : '' ?>">
        </div>

        <div class="col-md-6 mb-3">
          <label for="customer_dob">Date of Birth</label>
          <input type="date" class="form-control" name="customer_dob" id="customer_dob"
            value="<?= isset($customer) ? $customer['customer_dob'] : '' ?>">
        </div>

        <div class="col-md-6 mb-3">
          <label for="customer_gender">Gender</label>
          <select class="form-control" name="customer_gender" id="customer_gender">
            <option value="male" <?= isset($customer) && $customer['customer_gender'] == 'male' ? 'selected' : '' ?>>Male</option>
            <option value="female" <?= isset($customer) && $customer['customer_gender'] == 'female' ? 'selected' : '' ?>>Female</option>
            <option value="other" <?= isset($customer) && $customer['customer_gender'] == 'other' ? 'selected' : '' ?>>Other</option>
          </select>
        </div>

        <div class="col-12 mt-4">
          <a href="<?= base_url('/customers') ?>" class="btn btn-secondary">Cancel</a>
          <button type="submit" class="btn btn-primary"><?= isset($customer) ? 'Update' : 'Save' ?></button>
        </div>
      </div>
    </form>
  </div>
</div>
<?= $this->endSection() ?>