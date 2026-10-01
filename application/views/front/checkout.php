<?php $v = function ($k, $d = '') use ($old, $cust) { if (isset($old[$k])) return $old[$k]; if ($cust && isset($cust->$k)) return $cust->$k; return $d; }; ?>
<div class="container">
  <div class="page-head"><span class="eyebrow">Step 2 of 3</span><h1>Checkout</h1></div>
  <form method="post" action="<?= site_url('checkout/place') ?>" id="checkoutForm" novalidate>
    <?= csrf_field() ?>
    <div class="row g-5" style="padding-bottom:80px">
      <div class="col-lg-7">
        <div class="panel-d mb-4">
          <h4>Contact &amp; delivery</h4>
          <?php if (!$cust): ?><p style="font-size:14px;color:var(--mute)">Have an account? <a href="<?= site_url('account/login') ?>" style="text-decoration:underline;color:var(--ink)">Sign in</a> for faster checkout and order history.</p><?php endif; ?>
          <div class="row g-3">
            <div class="col-md-6"><label class="lbl" for="f_name">Full name *</label><input class="input-d" id="f_name" name="name" value="<?= e($v('name')) ?>" required autocomplete="name"></div>
            <div class="col-md-6"><label class="lbl" for="f_phone">Phone *</label><input class="input-d" id="f_phone" name="phone" value="<?= e($v('phone')) ?>" required autocomplete="tel"></div>
            <div class="col-12"><label class="lbl" for="f_email">Email *</label><input class="input-d" type="email" id="f_email" name="email" value="<?= e($v('email')) ?>" required autocomplete="email"></div>
            <div class="col-12"><label class="lbl" for="f_addr">Address *</label><input class="input-d" id="f_addr" name="address" value="<?= e($v('address')) ?>" required autocomplete="street-address" placeholder="House / flat, street, area"></div>
            <div class="col-md-4"><label class="lbl" for="f_city">City *</label><input class="input-d" id="f_city" name="city" value="<?= e($v('city')) ?>" required></div>
            <div class="col-md-4"><label class="lbl" for="f_state">State / Province</label><input class="input-d" id="f_state" name="state" value="<?= e($v('state')) ?>"></div>
            <div class="col-md-4"><label class="lbl" for="f_pc">Postal code</label><input class="input-d" id="f_pc" name="postal_code" value="<?= e($v('postal_code')) ?>"></div>
            <div class="col-12"><label class="lbl" for="f_cc">Country *</label>
              <select class="select-d" id="f_cc" name="country"><?php foreach (country_list() as $code => $nm): ?><option value="<?= $code ?>" <?= (isset($old['country']) ? $old['country'] : $cc) === $code ? 'selected' : '' ?>><?= e($nm) ?></option><?php endforeach; ?></select></div>
            <div class="col-12"><label class="lbl" for="f_notes">Order notes</label><textarea class="input-d" id="f_notes" name="notes" style="min-height:80px" placeholder="Delivery instructions (optional)"><?= e($v('notes')) ?></textarea></div>
          </div>
        </div>
        <div class="panel-d">
          <h4>Payment</h4>
          <?php $first = true; foreach ($methods as $k => $m): $on = (isset($old['payment_method']) ? $old['payment_method'] === $k : $first); $first = false; ?>
          <label class="pay-opt <?= $on ? 'on' : '' ?>"><input type="radio" name="payment_method" value="<?= $k ?>" <?= $on ? 'checked' : '' ?>><b><?= e($m[0]) ?></b><div style="font-size:13px;color:var(--mute);margin-left:26px"><?= e($m[1]) ?></div><?php if ($m[2]): ?><div class="det"><?= e($m[2]) ?></div><?php endif; ?></label>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="summary">
          <h4>Your order</h4>
          <?php foreach ($items as $i): ?>
          <div class="co-item">
            <div class="th <?= $i['type'] === 'package' ? 'outfit-thumb' : '' ?>" <?= $i['type'] === 'package' ? "data-outfit='" . e(json_encode($i['outfit'])) . "'" : '' ?>><?php if ($i['type'] === 'product'): ?><img src="<?= upload_url($i['image']) ?>" alt=""><?php endif; ?><span class="q"><?= (int)$i['qty'] ?></span></div>
            <div style="flex:1;min-width:0"><b style="font-size:14px;font-weight:600;line-height:1.3;display:block"><?= e($i['type'] === 'package' ? 'Custom Outfit Package' : $i['name']) ?></b><small><?= $i['type'] === 'product' ? e(trim($i['size'] . ($i['size'] && $i['color'] ? ' / ' : '') . $i['color'])) : count($i['meta']['lines']) . ' pieces' ?></small></div>
            <b><?= money($i['line_total']) ?></b>
          </div>
          <?php endforeach; ?>
          <div style="border-top:1px solid var(--line);margin-top:10px;padding-top:12px">
            <div class="sum-row"><span class="mut">Subtotal</span><span><?= money($t['subtotal']) ?></span></div>
            <?php if ($t['discount'] > 0): ?><div class="sum-row"><span class="mut">Discount (<?= e($t['coupon']) ?>)</span><span>-<?= money($t['discount']) ?></span></div><?php endif; ?>
            <div class="sum-row"><span class="mut">Shipping</span><span id="shipTxt"><?= $t['shipping'] > 0 ? money($t['shipping']) : 'Free' ?></span></div>
            <div class="sum-row total"><span>Total</span><span id="totalTxt"><?= money($t['total']) ?></span></div>
          </div>
          <button class="btn-d btn-block mt-3" id="placeBtn"><?= icon('lock', 16) ?> Place order</button>
          <p style="font-size:12.5px;color:var(--mute);margin:14px 0 0;text-align:center">By placing your order you agree to our terms and return policy.</p>
        </div>
      </div>
    </div>
  </form>
</div>
