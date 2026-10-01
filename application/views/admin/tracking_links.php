<div class="row g-3">
  <div class="col-lg-7"><div class="cardx"><div class="hd"><h5>Tracking link generator</h5></div><div class="bd">
    <p style="color:var(--mute)">Add a source tag to any link you share. Visits and orders from that link are then credited to the right platform on the Analytics page - even when the app hides the referrer (WhatsApp, Instagram bio, TikTok bio, ads).</p>
    <label class="fl">Page to link to</label>
    <select class="fi mb-3" id="tlPage"><option value="">Home page</option><option value="shop">All products</option><option value="customize">Outfit customizer</option><option value="demand">Order on demand</option><option value="shop?sale=1">Sale</option>
      <?php foreach ($this->db->order_by('id', 'DESC')->limit(60)->get_where('products', array('status' => 1))->result() as $p): ?><option value="product/<?= e($p->slug) ?>">Product: <?= e($p->name) ?></option><?php endforeach; ?></select>
    <label class="fl">Campaign name (optional)</label><input class="fi" id="tlCamp" placeholder="eid-sale">
    <div class="hint">Letters, numbers and dashes only.</div>
  </div></div></div>
  <div class="col-lg-5"><div class="cardx"><div class="hd"><h5>Your links</h5></div><div class="bd" id="tlOut"></div></div></div>
</div>
<script>
(function () {
  var base = "<?= base_url() ?>";
  var src = <?= json_encode(array_map(function ($k) { return array('key' => $k, 'label' => source_meta($k)['label'], 'icon' => source_icon($k, 18)); }, array('facebook', 'instagram', 'tiktok', 'whatsapp', 'youtube', 'google'))) ?>;
  function render() {
    var page = document.getElementById('tlPage').value, camp = document.getElementById('tlCamp').value.toLowerCase().replace(/[^a-z0-9-]/g, '-').replace(/^-+|-+$/g, '');
    var html = '';
    src.forEach(function (s, i) {
      var u = base + page; u += (page.indexOf('?') > -1 ? '&' : '?') + 'utm_source=' + s.key + (camp ? '&utm_campaign=' + camp : '');
      html += '<div class="mb-3"><div class="d-flex align-items-center justify-content-between mb-1"><b class="d-inline-flex align-items-center gap-2">' + s.icon + s.label + '</b><button type="button" class="btn-a2 btn-sm2" data-copy="#tl' + i + '">Copy</button></div><div class="tracklink" id="tl' + i + '"></div></div>';
    });
    document.getElementById('tlOut').innerHTML = html;
    src.forEach(function (s, i) { var u = base + page; u += (page.indexOf('?') > -1 ? '&' : '?') + 'utm_source=' + s.key + (camp ? '&utm_campaign=' + camp : ''); document.getElementById('tl' + i).textContent = u; });
  }
  document.getElementById('tlPage').addEventListener('change', render); document.getElementById('tlCamp').addEventListener('input', render); render();
})();
</script>
