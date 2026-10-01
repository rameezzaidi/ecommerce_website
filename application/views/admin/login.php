<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin Login | <?= e(setting('site_name', 'DRESSY')) ?></title>
<link rel="icon" href="<?= base_url('assets/img/favicon.svg') ?>" type="image/svg+xml">
<link rel="stylesheet" href="<?= asset('vendor/bootstrap/bootstrap.min.css') ?>"><link rel="stylesheet" href="<?= asset('css/admin.css') ?>"></head>
<body><div class="login-wrap"><form class="login-card" method="post" action="<?= site_url('admin/login') ?>">
  <?= csrf_field() ?>
  <div style="font-family:'Playfair Display',Georgia,serif;font-size:30px;letter-spacing:.14em;font-weight:600;text-align:center"><?= e(setting('site_name', 'DRESSY')) ?></div>
  <div style="text-align:center;color:var(--mute);font-size:11px;letter-spacing:.3em;margin:4px 0 28px">ADMIN PANEL</div>
  <?= flash_html() ?>
  <div class="mb-3"><label class="fl" for="em">Email</label><input class="fi" id="em" type="email" name="email" required autofocus autocomplete="username"></div>
  <div class="mb-4"><label class="fl" for="pw">Password</label><input class="fi" id="pw" type="password" name="password" required autocomplete="current-password"></div>
  <button class="btn-a w-100 justify-content-center" style="padding:12px"><?= icon('lock', 16) ?> Sign in</button>
</form></div></body></html>
