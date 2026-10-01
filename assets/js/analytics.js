(function () {
  'use strict';
  var C = {}, fmt = function (n) { return Number(n).toLocaleString(); };
  var cur = '';
  function money(n) { return cur + ' ' + Math.round(n).toLocaleString(); }
  function esc(s) { return String(s == null ? '' : s).replace(/[&<>"]/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]; }); }

  function chart(id, cfg) { if (C[id]) { C[id].data = cfg.data; C[id].update(); return; } C[id] = new Chart(document.getElementById(id), cfg); }

  function render(d) {
    cur = d.currency;
    document.getElementById('liveNow').textContent = d.live;
    document.getElementById('upd').textContent = d.updated;
    document.getElementById('kVisits').textContent = fmt(d.visits);
    document.getElementById('kPv').textContent = fmt(d.pageviews);

    // source cards (the four platforms you care about first)
    var main = ['facebook', 'instagram', 'tiktok', 'whatsapp'], by = {};
    d.sources.forEach(function (s) { by[s.key] = s; });
    var h = '';
    main.forEach(function (k) {
      var s = by[k] || { label: k, color: '#999', visits: 0, share: 0, orders: 0, revenue: 0 };
      h += '<div class="col-sm-6 col-xl-3"><div class="cardx" style="padding:18px 20px"><div class="d-flex align-items-center gap-3"><div class="src-ic" style="background:' + s.color + '">' + ANA.icons[k] + '</div><div><div style="font-size:12px;color:var(--mute);font-weight:600;letter-spacing:.06em;text-transform:uppercase">' + esc(s.label) + '</div><div style="font-size:24px;font-weight:700;letter-spacing:-.02em">' + fmt(s.visits) + '</div></div><div class="ms-auto" style="text-align:right"><b style="font-size:15px">' + s.share + '%</b><div style="font-size:12px;color:var(--mute)">' + s.orders + ' orders</div></div></div><div class="bar mt-3"><i style="width:' + Math.min(100, s.share) + '%;background:' + s.color + '"></i></div></div></div>';
    });
    document.getElementById('srcCards').innerHTML = h;

    // line chart
    chart('lineChart', { type: 'line', data: { labels: d.labels, datasets: d.series.map(function (s) { return { label: s.label, data: s.data, borderColor: s.color, backgroundColor: s.color + '22', tension: .35, pointRadius: d.labels.length > 31 ? 0 : 2.5, borderWidth: 2, fill: false }; }) },
      options: { maintainAspectRatio: false, interaction: { mode: 'index', intersect: false }, plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#eee' } }, x: { grid: { display: false }, ticks: { maxTicksLimit: 10 } } } } });

    // donut
    var act = d.sources.filter(function (s) { return s.visits > 0; });
    chart('donut', { type: 'doughnut', data: { labels: act.map(function (s) { return s.label; }), datasets: [{ data: act.map(function (s) { return s.visits; }), backgroundColor: act.map(function (s) { return s.color; }), borderWidth: 2, borderColor: '#fff' }] },
      options: { maintainAspectRatio: false, cutout: '64%', plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } } } } });

    // countries
    chart('countryChart', { type: 'bar', data: { labels: d.countries.map(function (c) { return c.country; }), datasets: [{ label: 'Visits', data: d.countries.map(function (c) { return +c.visits; }), backgroundColor: '#0a0a0a', borderRadius: 3, maxBarThickness: 22 }] },
      options: { indexAxis: 'y', maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#eee' } }, y: { grid: { display: false } } } } });

    // cities
    var tot = Math.max(1, d.visits), rows = '';
    d.cities.forEach(function (c) {
      var pct = (c.visits / tot * 100);
      rows += '<tr><td><span class="cc">' + esc(c.country_code || '--') + '</span><b>' + esc(c.city) + '</b><span style="color:var(--mute)">' + (c.region ? ', ' + esc(c.region) : '') + '</span></td><td>' + fmt(c.visits) + '</td><td><div class="d-flex align-items-center gap-2"><div class="bar"><i style="width:' + Math.min(100, pct * 2.2) + '%;background:#0a0a0a"></i></div><span style="font-size:12px;color:var(--mute);width:42px">' + pct.toFixed(1) + '%</span></div></td></tr>';
    });
    document.getElementById('cityRows').innerHTML = rows || '<tr><td colspan="3" style="text-align:center;color:var(--mute);padding:30px">No location data yet</td></tr>';

    // performance
    var p = '';
    d.sources.forEach(function (s) {
      p += '<tr><td><span class="d-inline-flex align-items-center gap-2"><span class="src-ic" style="width:26px;height:26px;background:' + s.color + '">' + ANA.icons[s.key].replace('width="20" height="20"', 'width="14" height="14"') + '</span><b>' + esc(s.label) + '</b></span></td><td style="text-align:right">' + fmt(s.visits) + '</td><td style="text-align:right">' + s.orders + '</td><td style="text-align:right">' + money(s.revenue) + '</td><td style="text-align:right"><b>' + s.conv + '%</b></td></tr>';
    });
    p += '<tr style="background:#fafaf9"><td><b>Total</b></td><td style="text-align:right"><b>' + fmt(d.visits) + '</b></td><td style="text-align:right"><b>' + d.orders + '</b></td><td style="text-align:right"><b>' + money(d.revenue) + '</b></td><td></td></tr>';
    document.getElementById('perfRows').innerHTML = p;

    // devices + pages
    var dc = { mobile: '#0a0a0a', desktop: '#8a8a85', tablet: '#c8c8c3' };
    chart('devChart', { type: 'doughnut', data: { labels: d.devices.map(function (x) { return x.device || 'unknown'; }), datasets: [{ data: d.devices.map(function (x) { return +x.c; }), backgroundColor: d.devices.map(function (x) { return dc[x.device] || '#ddd'; }), borderWidth: 2, borderColor: '#fff' }] }, options: { maintainAspectRatio: false, cutout: '62%', plugins: { legend: { position: 'right', labels: { usePointStyle: true, boxWidth: 8 } } } } });
    document.getElementById('pageRows').innerHTML = d.pages.map(function (x) { return '<div class="d-flex justify-content-between py-2" style="border-bottom:1px solid var(--line)"><span style="min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">' + esc(x.page) + '</span><b>' + fmt(x.c) + '</b></div>'; }).join('');
  }

  function load() { fetch(ANA.url, { credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(render).catch(function () { }); }
  document.addEventListener('DOMContentLoaded', function () { load(); setInterval(load, 30000); });
})();
