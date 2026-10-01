<?php $icons = array(); foreach (array_keys(source_meta()) as $k) $icons[$k] = source_icon($k, 20); ?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
  <div class="d-flex align-items-center gap-3"><span><span class="live-dot"></span><b id="liveNow">0</b> <span style="color:var(--mute)">visitors in the last 5 minutes</span></span><span style="color:var(--mute);font-size:12.5px">Updated <span id="upd">--:--:--</span> (auto-refresh 30s)</span></div>
  <div class="btn-group" role="group" aria-label="Range">
    <?php foreach (array(1 => 'Today', 7 => '7 days', 30 => '30 days', 90 => '90 days') as $v => $l): ?><a class="btn-a<?= $range == $v ? '' : '2' ?> btn-sm2" href="?range=<?= $v ?>"><?= $l ?></a><?php endforeach; ?>
  </div>
</div>

<div class="row g-3 mb-3" id="srcCards"></div>

<div class="row g-3 mb-3">
  <div class="col-xl-8"><div class="cardx"><div class="hd"><h5>Visits by source over time</h5><div style="color:var(--mute);font-size:13px"><b id="kVisits" style="color:var(--ink)">0</b> visits &middot; <b id="kPv" style="color:var(--ink)">0</b> page views</div></div><div class="bd"><div class="chart-box"><canvas id="lineChart"></canvas></div></div></div></div>
  <div class="col-xl-4"><div class="cardx h-100"><div class="hd"><h5>Source share</h5></div><div class="bd"><div class="chart-box" style="height:260px"><canvas id="donut"></canvas></div></div></div></div>
</div>

<div class="row g-3 mb-3">
  <div class="col-xl-6"><div class="cardx h-100"><div class="hd"><h5>Visitors by country</h5></div><div class="bd"><div class="chart-box" style="height:340px"><canvas id="countryChart"></canvas></div></div></div></div>
  <div class="col-xl-6"><div class="cardx h-100"><div class="hd"><h5>Top regions &amp; cities</h5></div><div class="table-responsive"><table class="tb"><thead><tr><th>Location</th><th>Visits</th><th style="width:34%">Share</th></tr></thead><tbody id="cityRows"></tbody></table></div></div></div>
</div>

<div class="row g-3 mb-3">
  <div class="col-xl-8"><div class="cardx"><div class="hd"><h5>Source performance &amp; conversion</h5><span style="color:var(--mute);font-size:12.5px">Orders are attributed to the visitor's first-touch source</span></div><div class="table-responsive"><table class="tb"><thead><tr><th>Source</th><th style="text-align:right">Visits</th><th style="text-align:right">Orders</th><th style="text-align:right">Revenue</th><th style="text-align:right">Conversion</th></tr></thead><tbody id="perfRows"></tbody></table></div></div></div>
  <div class="col-xl-4"><div class="cardx mb-3"><div class="hd"><h5>Devices</h5></div><div class="bd"><div class="chart-box" style="height:190px"><canvas id="devChart"></canvas></div></div></div>
  <div class="cardx"><div class="hd"><h5>Top pages</h5></div><div class="bd" style="padding:6px 22px" id="pageRows"></div></div></div>
</div>

<div class="cardx"><div class="bd d-flex align-items-center justify-content-between flex-wrap gap-3"><div><b>Is WhatsApp traffic showing as "Direct"?</b><div style="color:var(--mute);font-size:13px">WhatsApp and some in-app links hide the referrer. Use a tagged link so every click is credited correctly.</div></div><a class="btn-a" href="<?= site_url('admin/tracking-links') ?>"><?= icon('link-2', 15) ?> Create tracking links</a></div></div>

<script>window.ANA = { url: "<?= site_url('admin/analytics/data?range=' . $range) ?>", icons: <?= json_encode($icons) ?> };</script>
