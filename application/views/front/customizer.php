<div class="container">
  <div class="crumbs"><a href="<?= base_url() ?>">Home</a><span class="sep">/</span>Customize</div>
  <div class="page-head">
    <span class="eyebrow">Design studio</span><h1>Design your outfit</h1>
    <p>Choose jeans, a t-shirt or shirt, and shoes - all optional. Your selection appears on the mannequin instantly. Pick all three and save <?= (float)$pkg_discount ?>% on the package.</p>
  </div>

  <div class="cz" id="cz" style="padding-bottom:90px">
    <div>
      <div class="cz-stage" id="czStage"><span class="tag">Live preview</span><div id="czSvg"></div>
        <div class="cz-sum" id="czMini"></div></div>
    </div>

    <div>
      <!-- Jeans -->
      <div class="cz-block open" data-slot="jeans">
        <div class="hd"><h5><?= icon('minus-square', 18) ?> Jeans <span class="picked" data-picked>None selected</span></h5><span class="chev"><?= icon('chevron-down', 18) ?></span></div>
        <div class="bd"><div class="opts" data-opts></div><div class="size-row mt-3 d-none" data-sizebox><label class="lbl">Waist size</label><div class="size-pills" data-sizes></div></div></div>
      </div>
      <!-- Top -->
      <div class="cz-block open" data-slot="top">
        <div class="hd"><h5><?= icon('tag', 18) ?> T-Shirt / Shirt <span class="picked" data-picked>None selected</span></h5><span class="chev"><?= icon('chevron-down', 18) ?></span></div>
        <div class="bd"><div class="seg" id="topSeg"><button type="button" data-type="tshirt" class="on">T-Shirt</button><button type="button" data-type="shirt">Shirt</button></div><div class="opts" data-opts></div><div class="size-row mt-3 d-none" data-sizebox><label class="lbl">Size</label><div class="size-pills" data-sizes></div></div></div>
      </div>
      <!-- Shoes -->
      <div class="cz-block open" data-slot="shoes">
        <div class="hd"><h5><?= icon('chevrons-down', 18) ?> Shoes <span class="picked" data-picked>None selected</span></h5><span class="chev"><?= icon('chevron-down', 18) ?></span></div>
        <div class="bd"><div class="opts" data-opts></div><div class="size-row mt-3 d-none" data-sizebox><label class="lbl">Shoe size (EU)</label><div class="size-pills" data-sizes></div></div></div>
      </div>

      <div class="cz-foot">
        <div id="czLines"></div>
        <div class="cz-total"><span>Package total</span><span id="czTotal">Rs. 0</span></div>
        <div class="d-flex gap-3 align-items-center mt-3 flex-wrap">
          <div class="qty"><button type="button" id="czMinus" aria-label="Decrease">&minus;</button><input type="text" id="czQty" value="1" readonly aria-label="Quantity"><button type="button" id="czPlus" aria-label="Increase">+</button></div>
          <button class="btn-d flex-fill" id="czAdd" disabled><?= icon('shopping-bag', 17) ?> Add package to bag</button>
        </div>
        <button class="btn-o btn-sm mt-3" id="czReset" type="button"><?= icon('rotate-ccw', 14) ?> Reset outfit</button>
      </div>
    </div>
  </div>
</div>
<script>window.CZ = <?= json_encode(array('parts' => $grouped, 'discount' => $pkg_discount, 'currency' => setting('currency_symbol', 'Rs.'))) ?>;</script>
