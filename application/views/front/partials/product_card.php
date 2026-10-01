<?php /** @var object $p */
$disc = discount_percent($p);
$out = (int)$p->stock <= 0;
$isNew = !empty($p->is_new);
$wl = isset($wishlist) && in_array((int)$p->id, $wishlist);
?>
<article class="p-card">
  <div class="p-img">
    <a href="<?= site_url('product/' . $p->slug) ?>" aria-label="<?= e($p->name) ?>"><img src="<?= upload_url($p->image) ?>" alt="<?= e($p->name) ?>" loading="lazy"></a>
    <div class="p-badges">
      <?php if ($out): ?><span class="p-badge dark">Sold out</span><?php endif; ?>
      <?php if (!$out && $disc): ?><span class="p-badge dark">-<?= $disc ?>%</span><?php endif; ?>
      <?php if (!$out && $isNew): ?><span class="p-badge">New</span><?php endif; ?>
    </div>
    <button class="p-wish <?= $wl ? 'on' : '' ?>" data-wish="<?= (int)$p->id ?>" aria-label="Add to wishlist"><?= icon('heart', 18) ?></button>
    <div class="p-quick"><a href="<?= site_url($out && $p->allow_demand ? 'demand?product=' . $p->id : 'product/' . $p->slug) ?>"><?= $out ? ($p->allow_demand ? 'Order on demand' : 'View') : 'View & add to bag' ?></a></div>
  </div>
  <div class="p-body">
    <div class="p-cat"><?= e($p->category_name) ?></div>
    <a class="p-name" href="<?= site_url('product/' . $p->slug) ?>"><?= e($p->name) ?></a>
    <?php if (!empty($p->review_count)): ?><div class="p-rate"><?= stars($p->rating, 12) ?><span>(<?= (int)$p->review_count ?>)</span></div><?php endif; ?>
    <div class="price"><span class="now"><?= money(current_price($p)) ?></span><?php if ($disc): ?><span class="was"><?= money($p->price) ?></span><?php endif; ?></div>
  </div>
</article>
