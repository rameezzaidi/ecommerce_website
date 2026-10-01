<?php if (!$items): ?>
<div class="empty"><?= icon('shopping-bag') ?><h3>Your bag is empty</h3><p>Looks like you have not added anything yet.</p><a href="<?= site_url('shop') ?>" class="btn-d mt-2">Continue shopping</a></div>
<?php else: ?>
<div class="row g-5" id="cartRows">
  <div class="col-lg-8">
    <?php foreach ($items as $i): ?>
    <div class="cart-line">
      <?php if ($i['type'] === 'package'): ?><div class="thumb outfit-thumb" data-outfit='<?= e(json_encode($i['outfit'])) ?>'></div>
      <?php else: ?><a class="thumb" href="<?= site_url('product/' . $i['slug']) ?>"><img src="<?= upload_url($i['image']) ?>" alt="<?= e($i['name']) ?>"></a><?php endif; ?>
      <div>
        <?php if ($i['type'] === 'product'): ?>
          <a class="nm" href="<?= site_url('product/' . $i['slug']) ?>"><?= e($i['name']) ?></a>
          <div class="meta"><?= $i['size'] ? 'Size: ' . e($i['size']) . '<br>' : '' ?><?= $i['color'] ? 'Colour: ' . e($i['color']) . '<br>' : '' ?>Unit: <?= money($i['unit_price']) ?></div>
        <?php else: ?>
          <span class="nm">Custom Outfit Package</span>
          <div class="meta">
            <?php foreach ($i['meta']['lines'] as $l): ?><?= e(ucfirst($l['slot'] === 'top' ? $l['type'] : $l['slot'])) ?>: <?= e($l['label']) ?><?= $l['size'] ? ' (' . e($l['size']) . ')' : '' ?> - <?= money($l['price']) ?><br><?php endforeach; ?>
            <?php if ($i['meta']['package_saving'] > 0): ?><b>Full-outfit saving: -<?= money($i['meta']['package_saving']) ?></b><br><?php endif; ?>
          </div>
        <?php endif; ?>
        <div class="d-flex align-items-center gap-3 mt-3">
          <div class="qty"><button data-qty="<?= e($i['key']) ?>" data-d="-1" aria-label="Decrease">&minus;</button><input type="text" value="<?= (int)$i['qty'] ?>" readonly aria-label="Quantity"><button data-qty="<?= e($i['key']) ?>" data-d="1" aria-label="Increase">+</button></div>
          <button class="rm" data-remove="<?= e($i['key']) ?>">Remove</button>
        </div>
      </div>
      <div class="lt"><?= money($i['line_total']) ?></div>
    </div>
    <?php endforeach; ?>
    <a href="<?= site_url('shop') ?>" class="link-arrow mt-4"><?= icon('arrow-left', 14) ?> Continue shopping</a>
  </div>
  <div class="col-lg-4">
    <div class="summary">
      <h4>Order Summary</h4>
      <div class="sum-row"><span class="mut">Subtotal</span><b><?= money($subtotal) ?></b></div>
      <?php if ($discount > 0): ?><div class="sum-row"><span class="mut">Coupon <b>(<?= e($coupon) ?>)</b> <button class="rm" id="rmCoupon" style="font-size:11px;background:none;border:0;border-bottom:1px solid var(--mute-2);cursor:pointer;margin-left:6px">remove</button></span><b>-<?= money($discount) ?></b></div><?php endif; ?>
      <div class="sum-row"><span class="mut">Shipping</span><span class="mut">Calculated at checkout</span></div>
      <?php if ($free_over > 0): $left = max(0, $free_over - $total); ?>
        <div style="font-size:12.5px;color:var(--mute);margin-top:6px"><?= $left > 0 ? 'Add ' . money($left) . ' more for free domestic delivery' : 'Free domestic delivery unlocked' ?></div>
        <div class="free-bar"><i style="width:<?= min(100, round($total / $free_over * 100)) ?>%"></i></div>
      <?php endif; ?>
      <?php if (!$coupon): ?><form class="coupon" id="couponForm"><input type="text" name="code" placeholder="Coupon code" aria-label="Coupon code"><button type="submit">Apply</button></form><?php else: ?><div style="height:14px"></div><?php endif; ?>
      <div class="sum-row total"><span>Total</span><span><?= money($total) ?></span></div>
      <a href="<?= site_url('checkout') ?>" class="btn-d btn-block mt-3">Proceed to checkout</a>
    </div>
  </div>
</div>
<?php endif; ?>
