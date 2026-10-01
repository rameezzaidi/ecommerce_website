<?php $stages = array('pending', 'confirmed', 'processing', 'shipped', 'delivered'); $idx = array_search($o->status, $stages); ?>
<div class="container" style="padding-bottom:90px">
  <div class="crumbs"><a href="<?= site_url('account') ?>">My account</a><span class="sep">/</span>Order <?= e($o->order_no) ?></div>
  <div class="page-head"><span class="eyebrow">Placed <?= date('d M Y, h:i A', strtotime($o->created_at)) ?></span><h1>Order <?= e($o->order_no) ?></h1></div>
  <?php $this->load->view('front/partials/order_detail', array('o' => $o, 'items' => $items, 'stages' => $stages, 'idx' => $idx)); ?>
</div>
