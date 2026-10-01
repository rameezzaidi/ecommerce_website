<?php
$disc = discount_percent($p);
$out = (int)$p->stock <= 0;
$low = !$out && (int)$p->stock <= 5;
$wl = in_array((int)$p->id, $wishlist);
$url = site_url('product/' . $p->slug);
?>
<div class="container">
  <div class="crumbs"><a href="<?= base_url() ?>">Home</a><span class="sep">/</span><a href="<?= site_url('shop') ?>">Shop</a><span class="sep">/</span><a href="<?= site_url('shop/' . $p->category_slug) ?>"><?= e($p->category_name) ?></a><span class="sep">/</span><?= e($p->name) ?></div>

  <div class="row g-5 mt-0 pt-3">
    <div class="col-lg-7">
      <div class="gal">
        <div class="gal-thumbs">
          <?php foreach ($images as $i => $img): ?><button type="button" class="<?= $i ? '' : 'on' ?>" data-img="<?= upload_url($img) ?>" aria-label="Image <?= $i + 1 ?>"><img src="<?= upload_url($img) ?>" alt=""></button><?php endforeach; ?>
        </div>
        <div class="gal-main" id="galMain"><img src="<?= upload_url($images ? $images[0] : '') ?>" alt="<?= e($p->name) ?>" id="galImg">
          <div class="p-badges" style="top:14px;left:14px"><?php if ($out): ?><span class="p-badge dark">Sold out</span><?php elseif ($disc): ?><span class="p-badge dark">-<?= $disc ?>%</span><?php endif; ?></div>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="pd-info">
        <a class="eyebrow" href="<?= site_url('shop/' . $p->category_slug) ?>"><?= e($p->category_name) ?></a>
        <h1><?= e($p->name) ?></h1>
        <?php if ($p->review_count): ?><a href="#reviews" class="p-rate" style="font-size:13px"><?= stars($p->rating, 15) ?><span><?= $p->rating ?> &middot; <?= (int)$p->review_count ?> review<?= $p->review_count == 1 ? '' : 's' ?></span></a><?php endif; ?>
        <div class="pd-price price"><span class="now"><?= money(current_price($p)) ?></span><?php if ($disc): ?><span class="was"><?= money($p->price) ?></span><span class="off">SAVE <?= $disc ?>%</span><?php endif; ?></div>
        <p style="color:var(--mute);margin:14px 0 0"><?= e($p->short_description) ?></p>

        <form id="addForm" data-id="<?= (int)$p->id ?>">
          <?php if ($colors): ?>
          <div class="opt-title">Colour <span id="colorName"><?= e(key($colors)) ?></span></div>
          <div class="swatches"><?php $ci = 0; foreach ($colors as $nm => $hex): ?><label title="<?= e($nm) ?>"><input type="radio" name="color" value="<?= e($nm) ?>" data-name="<?= e($nm) ?>" <?= $ci++ == 0 ? 'checked' : '' ?>><i style="background:<?= e($hex) ?>"></i></label><?php endforeach; ?></div>
          <?php endif; ?>
          <?php if ($sizes): ?>
          <div class="opt-title">Size <span><a href="#sizeGuide" data-bs-toggle="modal" style="text-decoration:underline">Size guide</a></span></div>
          <div class="size-pills"><?php foreach ($sizes as $s): ?><label><input type="radio" name="size" value="<?= e($s) ?>" <?= $out ? 'disabled' : '' ?>><span><?= e($s) ?></span></label><?php endforeach; ?></div>
          <?php endif; ?>

          <?php if (!$out): ?>
          <div class="d-flex align-items-center gap-3 mt-4">
            <div class="qty"><button type="button" data-q="-1" aria-label="Decrease">&minus;</button><input type="text" id="qtyIn" name="qty" value="1" data-max="<?= (int)$p->stock ?>" aria-label="Quantity"><button type="button" data-q="1" aria-label="Increase">+</button></div>
            <span class="stock-note <?= $low ? 'low' : '' ?>" style="margin:0"><i></i><?= $low ? 'Only ' . (int)$p->stock . ' left' : 'In stock' ?></span>
          </div>
          <div class="pd-actions">
            <button type="submit" class="btn-d" id="addBtn"><?= icon('shopping-bag', 17) ?> Add to bag</button>
            <button type="button" class="btn-o p-wish-btn <?= $wl ? 'on' : '' ?>" data-wish="<?= (int)$p->id ?>" aria-label="Wishlist" style="padding:14px 18px"><?= icon('heart', 18) ?></button>
          </div>
          <button type="button" class="btn-o btn-block mt-2" id="buyNow">Buy it now</button>
          <?php else: ?>
          <div class="stock-note out" style="margin-top:22px"><i></i>Currently out of stock</div>
          <?php if ($p->allow_demand): ?>
          <div class="demand-box">
            <h5><?= icon('package', 18) ?> Need it anyway? Order on demand</h5>
            <p>Tell us your size and colour and we will source this piece for you, confirm a price and give you a delivery date. No payment until you accept.</p>
            <a href="<?= site_url('demand?product=' . $p->id) ?>" class="btn-d btn-block">Place demand order</a>
          </div>
          <?php endif; ?>
          <div class="pd-actions"><button type="button" class="btn-o btn-block p-wish-btn <?= $wl ? 'on' : '' ?>" data-wish="<?= (int)$p->id ?>"><?= icon('heart', 17) ?> Save to wishlist</button></div>
          <?php endif; ?>
        </form>

        <div class="share">Share
          <a href="https://wa.me/?text=<?= rawurlencode($p->name . ' ' . $url) ?>" target="_blank" rel="noopener" aria-label="Share on WhatsApp"><?= brand_icon('whatsapp', 15) ?></a>
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= rawurlencode($url) ?>" target="_blank" rel="noopener" aria-label="Share on Facebook"><?= brand_icon('facebook', 15) ?></a>
          <a href="<?= $url ?>" data-copy aria-label="Copy link"><?= icon('link', 15) ?></a>
        </div>

        <div class="accord mt-4">
          <div class="a-item open"><button type="button"><span>Description</span><?= icon('plus', 16, 'ic-pm') ?></button><div class="a-body"><?= nl2br(e($p->description)) ?><div class="mt-2" style="font-size:13px">SKU: <?= e($p->sku) ?></div></div></div>
          <div class="a-item"><button type="button"><span>Delivery &amp; payment</span><?= icon('plus', 16, 'ic-pm') ?></button><div class="a-body">Domestic delivery in 2-5 working days, international in 7-14 days. Free domestic shipping over <?= money(setting('free_shipping_over', 8000)) ?>. Pay by cash on delivery, bank transfer or mobile wallet.</div></div>
          <div class="a-item"><button type="button"><span>Returns &amp; exchange</span><?= icon('plus', 16, 'ic-pm') ?></button><div class="a-body">Not the right fit? Exchange within 7 days of delivery - unworn, with tags attached. Custom and on-demand orders are final sale.</div></div>
        </div>
      </div>
    </div>
  </div>

  <!-- reviews -->
  <section id="reviews" class="mt-5 pt-5">
    <div class="sec-head"><div><span class="eyebrow">Feedback</span><h2>Customer reviews</h2></div></div>
    <div class="row g-5">
      <div class="col-lg-7">
        <?php if (!$reviews): ?><p style="color:var(--mute)">No reviews yet - be the first to share your thoughts.</p><?php endif; ?>
        <?php foreach ($reviews as $r): ?><div class="rv"><?= stars($r->rating) ?> <b class="ms-2"><?= e($r->name) ?></b><small><?= date('d M Y', strtotime($r->created_at)) ?></small><p class="mt-2 mb-0"><?= e($r->comment) ?></p></div><?php endforeach; ?>
      </div>
      <div class="col-lg-5">
        <form class="panel-d" method="post" action="<?= site_url('product/review/' . $p->id) ?>">
          <?= csrf_field() ?>
          <h4>Write a review</h4>
          <div class="mb-3"><label class="lbl">Rating</label><div class="star-input"><?php for ($i = 5; $i >= 1; $i--): ?><input type="radio" id="st<?= $i ?>" name="rating" value="<?= $i ?>" <?= $i == 5 ? 'checked' : '' ?>><label for="st<?= $i ?>" title="<?= $i ?> stars"><svg viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg></label><?php endfor; ?></div></div>
          <div class="mb-3"><label class="lbl" for="rvn">Name</label><input class="input-d" id="rvn" name="name" required value="<?= $customer ? e($customer->name) : '' ?>"></div>
          <div class="mb-3"><label class="lbl" for="rvc">Review</label><textarea class="input-d" id="rvc" name="comment" required minlength="5"></textarea></div>
          <button class="btn-d btn-block">Submit review</button>
        </form>
      </div>
    </div>
  </section>

  <?php if ($related): ?>
  <section class="mt-5 pt-5 pb-5">
    <div class="sec-head"><div><span class="eyebrow">You may also like</span><h2>Related pieces</h2></div></div>
    <div class="row g-3 g-lg-4"><?php foreach ($related as $r): ?><div class="col-6 col-lg-3"><?php $this->load->view('front/partials/product_card', array('p' => $r, 'wishlist' => $wishlist)); ?></div><?php endforeach; ?></div>
  </section>
  <?php endif; ?>
</div>

<!-- size guide -->
<div class="modal fade" id="sizeGuide" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content" style="border-radius:0">
  <div class="modal-header"><h5 class="modal-title">Size guide (cm)</h5><button class="hdr-btn" data-bs-dismiss="modal" aria-label="Close"><?= icon('x', 20) ?></button></div>
  <div class="modal-body"><table class="tbl"><tr><th>Size</th><th>Chest</th><th>Waist</th><th>Length</th></tr>
    <tr><td>S</td><td>92</td><td>78</td><td>68</td></tr><tr><td>M</td><td>98</td><td>84</td><td>70</td></tr><tr><td>L</td><td>104</td><td>90</td><td>72</td></tr><tr><td>XL</td><td>110</td><td>96</td><td>74</td></tr><tr><td>XXL</td><td>116</td><td>102</td><td>76</td></tr></table>
    <p class="mt-3 mb-0" style="font-size:13px;color:var(--mute)">Measurements are body sizes. Between sizes? Go one size up for a relaxed fit.</p></div>
</div></div></div>

<?php if (!$out): ?>
<script>window.PD={needSize:<?= $sizes ? 'true' : 'false' ?>};</script>
<?php endif; ?>
