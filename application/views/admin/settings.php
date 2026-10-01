<form method="post" action="<?= site_url('admin/settings') ?>"><?= csrf_field() ?>
<div class="row g-3">
<?php foreach ($groups as $title => $fields): ?>
  <div class="col-xl-6"><div class="cardx h-100"><div class="hd"><h5><?= e($title) ?></h5></div><div class="bd"><div class="row g-3">
    <?php foreach ($fields as $k => $label): $p = explode('|', $label); $type = isset($p[1]) ? $p[1] : 'text'; $v = setting($k, ''); ?>
      <div class="col-12">
        <?php if ($type === 'switch'): ?><div class="d-flex align-items-center gap-2"><label class="sw"><input type="checkbox" name="<?= $k ?>" value="1" <?= $v === '1' ? 'checked' : '' ?>><i></i></label><span style="font-weight:500"><?= e($p[0]) ?></span></div>
        <?php elseif ($type === 'textarea'): ?><label class="fl"><?= e($p[0]) ?></label><textarea class="fi" name="<?= $k ?>" rows="3"><?= e($v) ?></textarea>
        <?php else: ?><label class="fl"><?= e($p[0]) ?></label><input class="fi" type="<?= $type === 'number' ? 'number' : 'text' ?>" step="any" name="<?= $k ?>" value="<?= e($v) ?>"><?php endif; ?>
      </div><?php endforeach; ?></div></div></div></div>
<?php endforeach; ?>
</div>
<div class="mt-3"><button class="btn-a"><?= icon('check', 16) ?> Save settings</button></div>
</form>
<form class="cardx mt-4" method="post" action="<?= site_url('admin/settings/password') ?>" style="max-width:640px"><?= csrf_field() ?><div class="hd"><h5>Admin account</h5></div><div class="bd"><div class="row g-3">
  <div class="col-12"><label class="fl">Display name</label><input class="fi" name="name" value="<?= e($admin->name) ?>"></div>
  <div class="col-md-6"><label class="fl">Current password</label><input class="fi" type="password" name="current_password" autocomplete="current-password" required></div>
  <div class="col-md-6"><label class="fl">New password (min 8)</label><input class="fi" type="password" name="new_password" autocomplete="new-password" required></div></div>
  <button class="btn-a mt-3">Update password</button></div></form>
