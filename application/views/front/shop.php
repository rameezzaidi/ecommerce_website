<?php
$base = $cat ? site_url('shop/' . $cat->slug) : site_url('shop');
function shop_qs($over = array()) {
    $q = array_merge($_GET, $over);
    foreach ($q as $k => $v) if ($v === '' || $v === null) unset($q[$k]);
    unset($q['page']);
    return $q ? '?' . http_build_query($q) : '';
}
$active_parent = $cat ? ($cat->parent_id ? $cat->parent_id : $cat->id) : 0;
?>
<div class="container">
  <div class="crumbs"><a href="<?= base_url() ?>">Home</a><span class="sep">/</span><a href="<?= site_url('shop') ?>">Shop</a><?php if ($parent_cat): ?><span class="sep">/</span><a href="<?= site_url('shop/' . $parent_cat->slug) ?>"><?= e($parent_cat->name) ?></a><?php endif; ?><?php if ($cat): ?><span class="sep">/</span><?= e($cat->name) ?><?php endif; ?></div>
  <div class="page-head">
    <span class="eyebrow"><?= $f['q'] !== '' ? 'Search results' : 'Collection' ?></span>
    <h1><?= $f['q'] !== '' ? '&ldquo;' . e($f['q']) . '&rdquo;' : e($cat ? $cat->name : 'Shop All') ?></h1>
    <?php if ($cat && $cat->description): ?><p><?= e($cat->description) ?></p><?php endif; ?>
  </div>

  <div class="row g-5">
    <div class="col-lg-3">
      <div class="offcanvas-lg offcanvas-start" tabindex="-1" id="filters" style="max-width:88vw">
        <div class="offcanvas-header d-lg-none" style="border-bottom:1px solid var(--line)"><h5 class="m-0">Filters</h5><button class="hdr-btn" data-bs-dismiss="offcanvas" data-bs-target="#filters" aria-label="Close"><?= icon('x', 22) ?></button></div>
        <div class="offcanvas-body d-block p-3 p-lg-0">
          <form method="get" action="<?= $base ?>" id="filterForm">
            <?php if ($f['q'] !== ''): ?><input type="hidden" name="q" value="<?= e($f['q']) ?>"><?php endif; ?>
            <?php if ($f['sort']): ?><input type="hidden" name="sort" value="<?= e($f['sort']) ?>"><?php endif; ?>
            <div class="f-block pt-0">
              <h6>Categories</h6>
              <div class="f-cat">
                <a href="<?= site_url('shop') . shop_qs() ?>" class="<?= !$cat ? 'on' : '' ?>">All products</a>
                <?php foreach ($nav_tree as $c): ?>
                  <a href="<?= site_url('shop/' . $c->slug) . shop_qs() ?>" class="<?= $cat && $cat->id == $c->id ? 'on' : '' ?>"><?= e($c->name) ?></a>
                  <?php if ($active_parent == $c->id && $c->children): ?><div class="sub"><?php foreach ($c->children as $ch): ?><a href="<?= site_url('shop/' . $ch->slug) . shop_qs() ?>" class="<?= $cat && $cat->id == $ch->id ? 'on' : '' ?>"><?= e($ch->name) ?></a><?php endforeach; ?></div><?php endif; ?>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="f-block">
              <h6>Price (<?= e(setting('currency_symbol', 'Rs.')) ?>)</h6>
              <div class="d-flex gap-2 align-items-center"><input class="input-d" type="number" name="min" min="0" placeholder="<?= $range[0] ?>" value="<?= e($f['min']) ?>" aria-label="Min price"><span>&ndash;</span><input class="input-d" type="number" name="max" min="0" placeholder="<?= $range[1] ?>" value="<?= e($f['max']) ?>" aria-label="Max price"></div>
            </div>
            <div class="f-block">
              <h6>Size</h6>
              <div class="size-pills"><?php foreach (array('XS', 'S', 'M', 'L', 'XL', 'XXL', '28', '30', '32', '34', '36', '40', '42', '44') as $s): ?><label><input type="radio" name="size" value="<?= $s ?>" <?= $f['size'] === (string)$s ? 'checked' : '' ?>><span><?= $s ?></span></label><?php endforeach; ?></div>
              <?php if ($f['size']): ?><a href="<?= $base . shop_qs(array('size' => '')) ?>" style="font-size:12px;color:var(--mute);text-decoration:underline">Clear size</a><?php endif; ?>
            </div>
            <div class="f-block">
              <h6>Availability</h6>
              <label class="d-check"><input type="checkbox" name="stock" value="1" <?= $f['stock'] ? 'checked' : '' ?>> In stock only</label>
              <label class="d-check"><input type="checkbox" name="sale" value="1" <?= $f['sale'] ? 'checked' : '' ?>> On sale</label>
            </div>
            <div class="d-flex gap-2 mt-3"><button class="btn-d btn-sm flex-fill">Apply</button><a href="<?= $base ?>" class="btn-o btn-sm">Reset</a></div>
          </form>
        </div>
      </div>
    </div>

    <div class="col-lg-9">
      <div class="toolbar">
        <div class="d-flex align-items-center gap-3">
          <button class="btn-o btn-sm d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#filters"><?= icon('sliders', 15) ?> Filters</button>
          <span><?= (int)$pg['total'] ?> product<?= $pg['total'] == 1 ? '' : 's' ?></span>
        </div>
        <div style="min-width:210px"><select class="select-d" id="sortSel" aria-label="Sort"><?php foreach (array('' => 'Newest', 'popular' => 'Best selling', 'price_asc' => 'Price: low to high', 'price_desc' => 'Price: high to low', 'rating' => 'Top rated') as $k => $v): ?><option value="<?= $base . shop_qs(array('sort' => $k)) ?>" <?= (string)$f['sort'] === (string)$k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select></div>
      </div>

      <?php if (!$items): ?>
        <div class="empty"><?= icon('search') ?><h3>No products found</h3><p>Try removing a filter or searching for something else.</p><a href="<?= site_url('shop') ?>" class="btn-d mt-2">Clear filters</a></div>
      <?php else: ?>
        <div class="row g-3 g-lg-4">
          <?php foreach ($items as $p): ?><div class="col-6 col-md-4"><?php $this->load->view('front/partials/product_card', array('p' => $p, 'wishlist' => $wishlist)); ?></div><?php endforeach; ?>
        </div>
        <?php if ($pg['pages'] > 1): ?>
        <nav class="pager" aria-label="Pagination">
          <?php $qs = $_GET; unset($qs['page']);
          $link = function ($n) use ($base, $qs) { return $base . '?' . http_build_query(array_merge($qs, array('page' => $n))); };
          if ($pg['page'] > 1): ?><a href="<?= $link($pg['page'] - 1) ?>" aria-label="Previous"><?= icon('chevron-left', 16) ?></a><?php endif;
          for ($n = 1; $n <= $pg['pages']; $n++):
            if ($n == 1 || $n == $pg['pages'] || abs($n - $pg['page']) <= 1): ?>
              <?= $n == $pg['page'] ? '<span class="cur">' . $n . '</span>' : '<a href="' . $link($n) . '">' . $n . '</a>' ?>
            <?php elseif (abs($n - $pg['page']) == 2): ?><span style="border:0">&hellip;</span><?php endif;
          endfor;
          if ($pg['page'] < $pg['pages']): ?><a href="<?= $link($pg['page'] + 1) ?>" aria-label="Next"><?= icon('chevron-right', 16) ?></a><?php endif; ?>
        </nav>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>
<div style="height:80px"></div>
