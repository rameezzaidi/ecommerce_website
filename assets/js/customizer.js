/* DRESSY outfit customizer */
(function ($) {
  'use strict';
  var CZ = window.CZ, parts = CZ.parts;
  var SIZES = { jeans: ['28', '30', '32', '34', '36', '38', '40'], top: ['S', 'M', 'L', 'XL', 'XXL'], shoes: ['39', '40', '41', '42', '43', '44', '45'] };
  var st = { jeans: 0, top: 0, topType: 'tshirt', shoes: 0, sizes: { jeans: '', top: '', shoes: '' }, qty: 1 };
  var money = window.dMoney;

  function find(type, id) { var l = parts[type] || []; for (var i = 0; i < l.length; i++) if (l[i].id === id) return l[i]; return null; }
  function sel(slot) { return slot === 'top' ? find(st.topType, st.top) : find(slot, st[slot]); }

  function buildOpts(slot) {
    var type = slot === 'top' ? st.topType : slot;
    var $o = $('.cz-block[data-slot=' + slot + '] [data-opts]').empty();
    $o.append('<button type="button" class="opt' + (!st[slot] ? ' on' : '') + '" data-id="0"><span class="none-ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><circle cx="12" cy="12" r="9"/><path d="M5.6 5.6l12.8 12.8"/></svg></span><b>None</b><small>Skip</small></button>');
    (parts[type] || []).forEach(function (p) {
      $o.append('<button type="button" class="opt' + (st[slot] === p.id ? ' on' : '') + '" data-id="' + p.id + '"><span class="sw" style="background:' + p.color + '"></span><b>' + $('<i>').text(p.name).html() + '</b><small>' + money(p.price) + '</small></button>');
    });
  }

  function buildSizes(slot) {
    var $b = $('.cz-block[data-slot=' + slot + ']'), $box = $b.find('[data-sizebox]');
    if (!st[slot]) { $box.addClass('d-none'); return; }
    $box.removeClass('d-none');
    var $s = $b.find('[data-sizes]').empty();
    SIZES[slot].forEach(function (z) {
      $s.append('<label><input type="radio" name="sz_' + slot + '" value="' + z + '"' + (st.sizes[slot] === z ? ' checked' : '') + '><span>' + z + '</span></label>');
    });
  }

  function render() {
    var o = {}, j = sel('jeans'), t = sel('top'), s = sel('shoes');
    if (j) o.jeans = { style: j.style, color: j.color };
    if (t) o.top = { type: t.type, style: t.style, color: t.color };
    if (s) o.shoes = { style: s.style, color: s.color };
    $('#czSvg').html(Outfit.svg(o));

    ['jeans', 'top', 'shoes'].forEach(function (slot) {
      var p = sel(slot);
      $('.cz-block[data-slot=' + slot + '] [data-picked]').text(p ? p.name + ' - ' + p.color_name : 'None selected');
    });

    var lines = [], sub = 0, labels = { jeans: 'Jeans', top: st.topType === 'tshirt' ? 'T-Shirt' : 'Shirt', shoes: 'Shoes' };
    [['jeans', j], ['top', t], ['shoes', s]].forEach(function (a) {
      if (a[1]) { sub += a[1].price; lines.push('<div class="cz-line"><span>' + labels[a[0]] + ' &middot; ' + $('<i>').text(a[1].name).html() + (st.sizes[a[0]] ? ' (' + st.sizes[a[0]] + ')' : '') + '</span><span>' + money(a[1].price) + '</span></div>'); }
    });
    var full = j && t && s, save = full ? Math.round(sub * CZ.discount / 100) : 0, total = sub - save;
    if (full && save) lines.push('<div class="cz-line save"><span>Full outfit saving (' + CZ.discount + '%)</span><span>-' + money(save) + '</span></div>');
    if (!lines.length) lines.push('<div class="cz-line" style="color:var(--mute)">Pick at least one piece to begin.</div>');
    else if (!full) lines.push('<div class="cz-line" style="color:var(--mute);font-size:13px">Add ' + [!j ? 'jeans' : '', !t ? 'a top' : '', !s ? 'shoes' : ''].filter(Boolean).join(', ') + ' to unlock ' + CZ.discount + '% off.</div>');
    $('#czLines').html(lines.join(''));
    $('#czTotal').text(money(total * st.qty));
    $('#czMini').html(full ? '<b>Complete outfit</b> &middot; ' + money(total) : (lines.length && sub ? '<b>' + money(sub) + '</b> &middot; partial outfit' : 'Your outfit will appear here'));

    var needSize = ['jeans', 'top', 'shoes'].some(function (k) { return st[k] && !st.sizes[k]; });
    $('#czAdd').prop('disabled', !sub).attr('title', needSize ? 'Please choose sizes' : '');
  }

  // events
  $('.cz-block > .hd').on('click', function () { $(this).parent().toggleClass('open'); });
  $(document).on('click', '.cz-block .opt', function () {
    var $b = $(this).closest('.cz-block'), slot = $b.data('slot'), id = parseInt($(this).data('id'), 10);
    st[slot] = id; if (!id) st.sizes[slot] = '';
    buildOpts(slot); buildSizes(slot); render();
  });
  $(document).on('change', '.cz-block [data-sizes] input', function () {
    var slot = $(this).closest('.cz-block').data('slot'); st.sizes[slot] = this.value; render();
  });
  $('#topSeg button').on('click', function () {
    $('#topSeg button').removeClass('on'); $(this).addClass('on');
    st.topType = $(this).data('type'); st.top = 0; st.sizes.top = '';
    buildOpts('top'); buildSizes('top'); render();
  });
  $('#czMinus').on('click', function () { st.qty = Math.max(1, st.qty - 1); $('#czQty').val(st.qty); render(); });
  $('#czPlus').on('click', function () { st.qty = Math.min(10, st.qty + 1); $('#czQty').val(st.qty); render(); });
  $('#czReset').on('click', function () {
    st.jeans = st.top = st.shoes = 0; st.sizes = { jeans: '', top: '', shoes: '' }; st.qty = 1; $('#czQty').val(1);
    ['jeans', 'top', 'shoes'].forEach(function (s) { buildOpts(s); buildSizes(s); }); render();
  });
  $('#czAdd').on('click', function () {
    var missing = ['jeans', 'top', 'shoes'].filter(function (k) { return st[k] && !st.sizes[k]; });
    if (missing.length) { window.dToast('Please choose a size for: ' + missing.map(function (m) { return m === 'top' ? 'top' : m; }).join(', '), true); return; }
    var $b = $(this).prop('disabled', true);
    $.post(window.DRESSY.base + 'customizer/add', {
      jeans: st.jeans, top: st.top, shoes: st.shoes, size_jeans: st.sizes.jeans, size_top: st.sizes.top, size_shoes: st.sizes.shoes, qty: st.qty
    }, function (r) {
      $b.prop('disabled', false); $('#bagCount').attr('data-n', r.count).text(r.count || ''); window.dToast(r.msg); window.dOpenBag();
    }, 'json').fail(function (x) { $b.prop('disabled', false); window.dToast((x.responseJSON && x.responseJSON.msg) || 'Could not add package', true); });
  });

  ['jeans', 'top', 'shoes'].forEach(function (s) { buildOpts(s); buildSizes(s); });
  render();
})(jQuery);
