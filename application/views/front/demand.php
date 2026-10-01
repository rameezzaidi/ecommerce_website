<?php $v = function ($k, $d = '') use ($old, $cust, $p) { if (isset($old[$k])) return $old[$k]; if ($cust && isset($cust->$k)) return $cust->$k; return $d; }; ?>
<div class="container" style="padding-bottom:90px">
  <div class="crumbs"><a href="<?= base_url() ?>">Home</a><span class="sep">/</span>Order on demand</div>
  <div class="page-head"><span class="eyebrow">Made available for you</span><h1>Order on demand</h1><p>Out of stock, or not in our catalogue yet? Tell us exactly what you need. We will source it, send you a quote and delivery estimate, and you confirm only if you are happy.</p></div>
  <div class="row g-5">
    <div class="col-lg-7">
      <form class="panel-d" method="post" action="<?= site_url('demand/submit') ?>" enctype="multipart/form-data" novalidate>
        <?= csrf_field() ?><input type="hidden" name="product_id" value="<?= $p ? (int)$p->id : '' ?>">
        <h4>What do you need?</h4>
        <div class="row g-3">
          <div class="col-12"><label class="lbl" for="pn">Product name *</label><input class="input-d" id="pn" name="product_name" required value="<?= e($v('product_name', $p ? $p->name : '')) ?>" placeholder="e.g. Black slim-fit shirt"></div>
          <div class="col-md-4"><label class="lbl">Size</label><?php if ($sizes): ?><select class="select-d" name="size"><option value="">Select</option><?php foreach ($sizes as $s): ?><option <?= $v('size') === $s ? 'selected' : '' ?>><?= e($s) ?></option><?php endforeach; ?></select><?php else: ?><input class="input-d" name="size" value="<?= e($v('size')) ?>" placeholder="M / 32 / 42"><?php endif; ?></div>
          <div class="col-md-4"><label class="lbl">Colour</label><?php if ($colors): ?><select class="select-d" name="color"><option value="">Select</option><?php foreach ($colors as $n => $h): ?><option <?= $v('color') === $n ? 'selected' : '' ?>><?= e($n) ?></option><?php endforeach; ?></select><?php else: ?><input class="input-d" name="color" value="<?= e($v('color')) ?>" placeholder="Black"><?php endif; ?></div>
          <div class="col-md-4"><label class="lbl">Quantity *</label><input class="input-d" type="number" name="qty" min="1" max="99" value="<?= e($v('qty', 1)) ?>" required></div>
          <div class="col-12"><label class="lbl">Details</label><textarea class="input-d" name="description" placeholder="Fabric, fit, brand, link to a photo - anything that helps us find the exact piece."><?= e($v('description')) ?></textarea></div>
          <div class="col-md-6"><label class="lbl">Reference photo</label><input class="input-d" type="file" name="reference_image" accept="image/*"></div>
          <div class="col-md-6"><label class="lbl">Budget (<?= e(setting('currency_symbol', 'Rs.')) ?>)</label><input class="input-d" type="number" name="budget" min="0" value="<?= e($v('budget')) ?>" placeholder="Optional"></div>
        </div>
        <h4 class="mt-5">Your details</h4>
        <div class="row g-3">
          <div class="col-md-6"><label class="lbl">Full name *</label><input class="input-d" name="name" required value="<?= e($v('name')) ?>"></div>
          <div class="col-md-6"><label class="lbl">Phone / WhatsApp *</label><input class="input-d" name="phone" required value="<?= e($v('phone')) ?>"></div>
          <div class="col-12"><label class="lbl">Email *</label><input class="input-d" type="email" name="email" required value="<?= e($v('email')) ?>"></div>
          <div class="col-12"><label class="lbl">Delivery address</label><input class="input-d" name="address" value="<?= e($v('address')) ?>"></div>
          <div class="col-md-6"><label class="lbl">City</label><input class="input-d" name="city" value="<?= e($v('city')) ?>"></div>
          <div class="col-md-6"><label class="lbl">Country</label><select class="select-d" name="country"><?php foreach (country_list() as $nm): ?><option <?= ($v('country', 'Pakistan')) === $nm ? 'selected' : '' ?>><?= e($nm) ?></option><?php endforeach; ?></select></div>
        </div>
        <button class="btn-d mt-4 btn-block">Submit request</button>
      </form>
    </div>
    <div class="col-lg-5">
      <?php if ($p): ?><div class="panel-d mb-4 d-flex gap-3 align-items-center"><div style="width:90px;flex:none;aspect-ratio:4/5;background:var(--soft);overflow:hidden"><img src="<?= upload_url($p->image) ?>" alt="" style="width:100%;height:100%;object-fit:cover"></div><div><div class="p-cat">Requesting</div><b style="font-weight:600"><?= e($p->name) ?></b><div class="price mt-1"><span class="now"><?= money(current_price($p)) ?></span></div></div></div><?php endif; ?>
      <div class="panel-d"><h4 style="font-size:20px">How it works</h4>
        <div class="d-flex gap-3 mb-3"><b style="font-family:var(--serif);font-size:26px;line-height:1">1</b><div><b>Send your request</b><div style="color:var(--mute);font-size:14px">Describe the piece and add a photo if you have one.</div></div></div>
        <div class="d-flex gap-3 mb-3"><b style="font-family:var(--serif);font-size:26px;line-height:1">2</b><div><b>We quote you</b><div style="color:var(--mute);font-size:14px">Within 24-48 hours you receive a price and delivery estimate.</div></div></div>
        <div class="d-flex gap-3"><b style="font-family:var(--serif);font-size:26px;line-height:1">3</b><div><b>You decide</b><div style="color:var(--mute);font-size:14px">Accept to confirm the order - or decline at no cost.</div></div></div>
      </div>
    </div>
  </div>
</div>
