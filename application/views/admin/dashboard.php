<div class="row g-3 mb-3">
  <?php $tiles = array(
    array('dollar-sign', 'Revenue (30 days)', money($kpi['revenue_30']), 'All time ' . money($kpi['revenue'])),
    array('shopping-bag', 'Orders', $kpi['orders'], $kpi['orders_today'] . ' today'),
    array('activity', 'Visits (30 days)', number_format($kpi['visits_30']), 'See Traffic Analytics'),
    array('package', 'Open demand requests', $kpi['demands'], $kpi['customers'] . ' customers - ' . $kpi['products'] . ' products'),
  ); foreach ($tiles as $t): ?>
  <div class="col-sm-6 col-xl-3"><div class="cardx kpi"><div class="ico"><?= icon($t[0], 22) ?></div><div><small><?= $t[1] ?></small><b><?= $t[2] ?></b><em><?= $t[3] ?></em></div></div></div>
  <?php endforeach; ?>
</div>
<div class="row g-3">
  <div class="col-xl-8"><div class="cardx"><div class="hd"><h5>Sales - last 14 days</h5><a class="btn-a2 btn-sm2" href="<?= site_url('admin/analytics') ?>">Traffic analytics <?= icon('arrow-right', 13) ?></a></div><div class="bd"><div class="chart-box"><canvas id="salesChart"></canvas></div></div></div></div>
  <div class="col-xl-4"><div class="cardx h-100"><div class="hd"><h5>Best sellers</h5></div><div class="bd" style="padding:8px 22px">
    <?php foreach ($top as $p): ?><div class="d-flex gap-3 align-items-center py-2" style="border-bottom:1px solid var(--line)"><img class="thumb" src="<?= upload_url($p->image) ?>" alt=""><div style="flex:1;min-width:0"><b style="display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($p->name) ?></b><span style="color:var(--mute);font-size:12.5px"><?= (int)$p->sold_count ?> sold</span></div><b><?= money(current_price($p)) ?></b></div><?php endforeach; ?></div></div></div>

  <div class="col-xl-8"><div class="cardx"><div class="hd"><h5>Recent orders</h5><a class="btn-a2 btn-sm2" href="<?= site_url('admin/orders') ?>">All orders</a></div><div class="table-responsive"><table class="tb"><thead><tr><th>Order</th><th>Customer</th><th>Source</th><th>Status</th><th style="text-align:right">Total</th></tr></thead><tbody>
    <?php foreach ($recent as $o): ?><tr><td><a href="<?= site_url('admin/orders/view/' . $o->id) ?>"><b><?= e($o->order_no) ?></b></a><div style="color:var(--mute);font-size:12px"><?= time_ago($o->created_at) ?></div></td><td><?= e($o->name) ?><div style="color:var(--mute);font-size:12px"><?= e($o->city) ?>, <?= e($o->country_code) ?></div></td><td><span class="d-inline-flex align-items-center gap-2"><?= source_icon($o->source, 15) ?><?= e(source_meta($o->source)['label']) ?></span></td><td><?= status_badge($o->status) ?></td><td style="text-align:right"><b><?= money($o->total) ?></b></td></tr><?php endforeach; ?></tbody></table></div></div></div>
  <div class="col-xl-4">
    <div class="cardx mb-3"><div class="hd"><h5>Low stock</h5></div><div class="bd" style="padding:6px 22px"><?php if (!$low): ?><p class="my-3" style="color:var(--mute)">All products are well stocked.</p><?php endif; foreach ($low as $p): ?><div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--line)"><a href="<?= site_url('admin/products/edit/' . $p->id) ?>"><?= e($p->name) ?></a><?= (int)$p->stock <= 0 ? '<span class="badge-st st-bad">Sold out</span>' : '<span class="badge-st st-warn">' . (int)$p->stock . ' left</span>' ?></div><?php endforeach; ?></div></div>
    <div class="cardx"><div class="hd"><h5>Latest demand requests</h5></div><div class="bd" style="padding:6px 22px"><?php foreach ($demands as $d): ?><a class="d-flex justify-content-between gap-2 py-2" style="border-bottom:1px solid var(--line)" href="<?= site_url('admin/demands/view/' . $d->id) ?>"><span style="min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($d->product_name) ?></span><?= status_badge($d->status) ?></a><?php endforeach; ?></div></div>
  </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
  fetch('<?= site_url('admin/dashboard/sales_data') ?>', {credentials: 'same-origin'}).then(function (r) { return r.json(); }).then(function (d) {
    new Chart(document.getElementById('salesChart'), { data: { labels: d.labels, datasets: [
      { type: 'bar', label: 'Revenue', data: d.revenue, backgroundColor: '#0a0a0a', borderRadius: 4, yAxisID: 'y', maxBarThickness: 26 },
      { type: 'line', label: 'Orders', data: d.orders, borderColor: '#8a8a85', backgroundColor: '#8a8a85', tension: .35, yAxisID: 'y1', pointRadius: 3 }
    ] }, options: { maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, plugins: { legend: { position: 'bottom' } },
      scales: { y: { beginAtZero: true, grid: { color: '#eee' } }, y1: { beginAtZero: true, position: 'right', grid: { display: false }, ticks: { precision: 0 } }, x: { grid: { display: false } } } } });
  });
});
</script>
