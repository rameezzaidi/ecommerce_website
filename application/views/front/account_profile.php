<div class="container" style="max-width:720px;padding-bottom:90px">
  <div class="page-head"><span class="eyebrow">My account</span><h1>Profile</h1></div>
  <form class="panel-d" method="post" action="<?= site_url('account/profile') ?>"><?= csrf_field() ?>
    <div class="row g-3">
      <div class="col-md-6"><label class="lbl">Full name</label><input class="input-d" name="name" value="<?= e($customer->name) ?>" required></div>
      <div class="col-md-6"><label class="lbl">Phone</label><input class="input-d" name="phone" value="<?= e($customer->phone) ?>"></div>
      <div class="col-12"><label class="lbl">Email</label><input class="input-d" value="<?= e($customer->email) ?>" disabled></div>
      <div class="col-12"><label class="lbl">Address</label><input class="input-d" name="address" value="<?= e($customer->address) ?>"></div>
      <div class="col-md-6"><label class="lbl">City</label><input class="input-d" name="city" value="<?= e($customer->city) ?>"></div>
      <div class="col-md-6"><label class="lbl">Country</label><select class="select-d" name="country"><?php foreach (country_list() as $nm): ?><option <?= $customer->country === $nm ? 'selected' : '' ?>><?= e($nm) ?></option><?php endforeach; ?></select></div>
      <div class="col-12"><label class="lbl">New password <span style="text-transform:none;letter-spacing:0;font-weight:400;color:var(--mute)">(leave blank to keep)</span></label><input class="input-d" type="password" name="password" autocomplete="new-password"></div>
    </div>
    <button class="btn-d mt-4">Save changes</button> <a href="<?= site_url('account') ?>" class="btn-o mt-4">Back</a>
  </form>
</div>
