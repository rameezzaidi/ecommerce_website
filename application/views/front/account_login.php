<div class="container" style="max-width:480px;padding:60px 0 100px">
  <div class="text-center mb-4"><span class="eyebrow"><?= $mode === 'login' ? 'Welcome back' : 'Join us' ?></span><h1 style="font-size:40px;margin:8px 0 0"><?= $mode === 'login' ? 'Sign in' : 'Create account' ?></h1></div>
  <form class="panel-d" method="post" action="<?= site_url('account/' . $mode) ?>">
    <?= csrf_field() ?>
    <?php if ($mode === 'register'): ?>
      <div class="mb-3"><label class="lbl" for="n">Full name</label><input class="input-d" id="n" name="name" required></div>
      <div class="mb-3"><label class="lbl" for="p">Phone</label><input class="input-d" id="p" name="phone" required></div>
    <?php endif; ?>
    <div class="mb-3"><label class="lbl" for="e">Email</label><input class="input-d" type="email" id="e" name="email" required autocomplete="email"></div>
    <div class="mb-4"><label class="lbl" for="pw">Password</label><input class="input-d" type="password" id="pw" name="password" required minlength="<?= $mode === 'register' ? 6 : 1 ?>" autocomplete="<?= $mode === 'login' ? 'current-password' : 'new-password' ?>"></div>
    <button class="btn-d btn-block"><?= $mode === 'login' ? 'Sign in' : 'Create account' ?></button>
    <p class="text-center mt-4 mb-0" style="font-size:14px;color:var(--mute)"><?= $mode === 'login' ? 'New here? <a href="' . site_url('account/register') . '" style="color:var(--ink);text-decoration:underline">Create an account</a>' : 'Already registered? <a href="' . site_url('account/login') . '" style="color:var(--ink);text-decoration:underline">Sign in</a>' ?></p>
  </form>
</div>
