<div class="container">
  <div class="page-head"><span class="eyebrow">Step 1 of 3</span><h1>Shopping bag</h1></div>
  <div id="cartWrap" style="min-height:300px;padding-bottom:80px"><?php $this->load->view('front/partials/cart_table', array('items' => $items, 'subtotal' => $subtotal, 'discount' => $discount, 'coupon' => $coupon, 'free_over' => $free_over, 'total' => $total)); ?></div>
</div>
