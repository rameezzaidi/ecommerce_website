<?php if (!$items): ?>
<div class="drawer-b d-flex flex-column align-items-center justify-content-center text-center" style="flex:1">
  <?= icon('shopping-bag', 54, 'mb-3') ?><h5>Your bag is empty</h5><p style="color:var(--mute)">Discover something you will love.</p>
  <a href="<?= site_url('shop') ?>" class="btn-d">Start shopping</a>
</div>
<?php else: ?>
<div class="drawer-b">
  <?php foreach ($items as $i): ?>
  <div class="mini-line">
    <?php if ($i['type'] === 'package'): ?><div class="th outfit-thumb" data-outfit='<?= e(json_encode($i['outfit'])) ?>'></div>
    <?php else: ?><a class="th" href="<?= site_url('product/' . $i['slug']) ?>"><img src="<?= upload_url($i['image']) ?>" alt=""></a><?php endif; ?>
    <div style="flex:1;min-width:0">
      <div style="font-weight:600;font-size:14px;line-height:1.35"><?= e($i['name']) ?></div>
      <div style="font-size:12.5px;color:var(--mute)"><?= $i['type'] === 'product' ? e(trim($i['size'] . ' ' . ($i['size'] && $i['color'] ? '/' : '') . ' ' . $i['color'])) : 'Custom package' ?></div>
      <div class="d-flex justify-content-between align-items-center mt-2">
        <span style="font-size:13px;color:var(--mute)">Qty <?= (int)$i['qty'] ?></span><b><?= money($i['line_total']) ?></b>
      </div>
      <button class="rm" data-remove="<?= e($i['key']) ?>" style="font-size:11.5px;letter-spacing:.1em;text-transform:uppercase;background:none;border:0;border-bottom:1px solid var(--line);padding:0;color:var(--mute);margin-top:6px;cursor:pointer">Remove</button>
    </div>
  </div>
  <?php endforeach; ?>
</div>
<div class="drawer-f">
  <?php if ($free_over > 0): $left = max(0, $free_over - $total); ?>
    <div style="font-size:13px;margin-bottom:6px"><?= $left > 0 ? 'Add <b>' . money($left) . '</b> more for free delivery' : 'You have unlocked free delivery' ?></div>
    <div class="free-bar" style="margin-bottom:16px"><i style="width:<?= min(100, round($total / $free_over * 100)) ?>%"></i></div>
  <?php endif; ?>
  <div class="sum-row" style="padding-top:0"><span>Subtotal</span><b><?= money($subtotal) ?></b></div>
  <a href="<?= site_url('checkout') ?>" class="btn-d btn-block mt-2">Checkout</a>
  <a href="<?= site_url('cart') ?>" class="btn-o btn-block mt-2">View bag</a>
</div>
<?php endif; ?>
