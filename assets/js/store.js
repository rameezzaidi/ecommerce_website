/* DRESSY storefront behaviour */
(function ($) {
  'use strict';
  var D = window.DRESSY || {};

  function cookie(n) { var m = document.cookie.match(new RegExp('(?:^|; )' + n + '=([^;]*)')); return m ? decodeURIComponent(m[1]) : ''; }

  // CSRF: attach the token to every same-origin POST
  $.ajaxPrefilter(function (opt, orig, xhr) {
    if (opt.type && opt.type.toUpperCase() === 'POST' && !opt.crossDomain) {
      var tok = D.csrfHash || cookie(D.csrfCookie);
      if (typeof opt.data === 'string') opt.data += (opt.data ? '&' : '') + encodeURIComponent(D.csrfName) + '=' + encodeURIComponent(tok);
      else if (opt.data instanceof FormData) opt.data.append(D.csrfName, tok);
      else { opt.data = $.extend({}, opt.data); opt.data[D.csrfName] = tok; opt.data = $.param(opt.data); }
    }
  });

  function money(n) { return D.currency + ' ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ','); }
  window.dMoney = money;

  function toast(msg, err) {
    var $t = $('<div class="toast-d' + (err ? ' err' : '') + '" role="status"></div>').text(msg);
    $('#toasts').append($t);
    setTimeout(function () { $t.fadeOut(300, function () { $t.remove(); }); }, 3200);
  }
  window.dToast = toast;

  function setBag(n) { var $c = $('#bagCount'); $c.attr('data-n', n).text(n || ''); }
  function setWish(n) { var $c = $('#wishCount'); $c.attr('data-n', n).text(n || ''); }

  /* ---------- search ---------- */
  $('#openSearch').on('click', function () { $('#searchBox').addClass('open'); setTimeout(function () { $('#searchBox input').trigger('focus'); }, 150); });
  $('#closeSearch').on('click', function () { $('#searchBox').removeClass('open'); });
  $(document).on('keydown', function (e) { if (e.key === 'Escape') { $('#searchBox').removeClass('open'); closeDrawer(); } });

  /* ---------- bag drawer ---------- */
  function openDrawer() {
    $('#drawerInner').html('<div class="drawer-b d-flex align-items-center justify-content-center" style="flex:1"><span class="spin" style="width:26px;height:26px;border:2px solid #ddd;border-top-color:#000;border-radius:50%;display:block"></span></div>');
    $('body').addClass('drawer-open');
    $.get(D.base + 'cart/drawer', function (html) { $('#drawerInner').html(html); if (window.Outfit) Outfit.hydrate(document.getElementById('drawerInner')); });
  }
  function closeDrawer() { $('body').removeClass('drawer-open'); }
  window.dOpenBag = openDrawer;
  $('#openBag').on('click', openDrawer);
  $('#closeBag,#drawerBg').on('click', closeDrawer);
  $(document).on('click', '#drawerInner [data-remove]', function () {
    var key = $(this).data('remove');
    $.post(D.base + 'cart/remove', { key: key }, function (r) { setBag(r.count); openDrawer(); }, 'json');
  });

  /* ---------- wishlist ---------- */
  $(document).on('click', '[data-wish]', function (e) {
    e.preventDefault(); e.stopPropagation();
    var $b = $(this), id = $b.data('wish');
    $.post(D.base + 'wishlist/toggle', { product_id: id }, function (r) {
      $('[data-wish="' + id + '"]').toggleClass('on', r.on);
      setWish(r.count);
      toast(r.on ? 'Saved to your wishlist' : 'Removed from wishlist');
    }, 'json');
  });

  /* ---------- product page ---------- */
  var $gal = $('#galMain');
  $('.gal-thumbs button').on('click', function () {
    $('.gal-thumbs button').removeClass('on'); $(this).addClass('on'); $('#galImg').attr('src', $(this).data('img'));
  });
  $gal.on('mousemove', function (e) {
    if (window.innerWidth < 992) return;
    var r = this.getBoundingClientRect();
    $('#galImg').css('transform-origin', ((e.clientX - r.left) / r.width * 100) + '% ' + ((e.clientY - r.top) / r.height * 100) + '%');
  }).on('click', function () { if (window.innerWidth >= 992) $(this).toggleClass('zoom'); }).on('mouseleave', function () { $(this).removeClass('zoom'); });
  $('input[name=color]').on('change', function () { $('#colorName').text($(this).data('name')); });
  $('.qty [data-q]').on('click', function () {
    var $i = $('#qtyIn'), v = (parseInt($i.val(), 10) || 1) + parseInt($(this).data('q'), 10), mx = parseInt($i.data('max'), 10) || 99;
    $i.val(Math.max(1, Math.min(mx, v)));
  });
  $('.accord .a-item > button').on('click', function () { $(this).parent().toggleClass('open'); });
  $('[data-copy]').on('click', function (e) { e.preventDefault(); var u = this.href; (navigator.clipboard ? navigator.clipboard.writeText(u) : $.Deferred().resolve()).then ? toast('Link copied') : toast('Link copied'); });

  function addToCart(buy) {
    var $f = $('#addForm');
    if (window.PD && PD.needSize && !$f.find('input[name=size]:checked').length) { toast('Please select a size', true); $('.size-pills').first().css('outline', '1px solid #000'); return; }
    var $b = $('#addBtn').prop('disabled', true);
    $.post(D.base + 'cart/add', { product_id: $f.data('id'), qty: $('#qtyIn').val(), size: $f.find('input[name=size]:checked').val() || '', color: $f.find('input[name=color]:checked').val() || '' }, function (r) {
      $b.prop('disabled', false); setBag(r.count);
      if (buy) { window.location = D.base + 'checkout'; } else { toast(r.msg); openDrawer(); }
    }, 'json').fail(function (x) {
      $b.prop('disabled', false);
      toast((x.responseJSON && x.responseJSON.msg) || 'Could not add to bag', true);
    });
  }
  $('#addForm').on('submit', function (e) { e.preventDefault(); if ($('#addBtn').length) addToCart(false); });
  $('#buyNow').on('click', function () { addToCart(true); });

  /* ---------- cart page ---------- */
  function cartPost(url, data) {
    $.post(D.base + url, data, function (r) {
      $('#cartWrap').html(r.html); setBag(r.count); if (window.Outfit) Outfit.hydrate(document.getElementById('cartWrap'));
      if (r.msg) toast(r.msg, !r.ok);
    }, 'json').fail(function (x) { if (x.responseJSON) { toast(x.responseJSON.msg, true); if (x.responseJSON.html) $('#cartWrap').html(x.responseJSON.html); } });
  }
  $(document).on('click', '#cartWrap [data-qty]', function () {
    var $i = $(this).siblings('input'), v = (parseInt($i.val(), 10) || 1) + parseInt($(this).data('d'), 10);
    cartPost('cart/update', { key: $(this).data('qty'), qty: v });
  });
  $(document).on('click', '#cartWrap [data-remove]', function () { cartPost('cart/remove', { key: $(this).data('remove') }); });
  $(document).on('submit', '#couponForm', function (e) { e.preventDefault(); cartPost('cart/coupon', { code: $(this).find('input').val() }); });
  $(document).on('click', '#rmCoupon', function () { cartPost('cart/coupon', { remove: 1 }); });

  /* ---------- checkout ---------- */
  $(document).on('change', '.pay-opt input', function () { $('.pay-opt').removeClass('on'); $(this).closest('.pay-opt').addClass('on'); });
  $('#f_cc').on('change', function () {
    $.post(D.base + 'checkout/quote', { country: this.value }, function (r) { $('#shipTxt').text(r.shipping_txt); $('#totalTxt').text(r.total_txt); }, 'json');
  });
  $('#checkoutForm').on('submit', function (e) {
    var ok = true;
    $(this).find('[required]').each(function () { var bad = !$.trim(this.value); $(this).css('border-color', bad ? '#000' : ''); if (bad) ok = false; });
    if (!ok) { e.preventDefault(); toast('Please fill in all required fields', true); return; }
    $('#placeBtn').prop('disabled', true).text('Placing order...');
  });

  /* ---------- sort select ---------- */
  $('#sortSel').on('change', function () { window.location = this.value; });

  /* ---------- newsletter ---------- */
  $('#newsForm').on('submit', function (e) {
    e.preventDefault();
    $.post(D.base + 'pages/subscribe', { email: $(this).find('input').val() }, function (r) { toast(r.msg); $('#newsForm input').val(''); }, 'json')
      .fail(function (x) { toast((x.responseJSON && x.responseJSON.msg) || 'Something went wrong', true); });
  });

  /* ---------- scroll reveal ---------- */
  if ('IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (en) { en.forEach(function (x) { if (x.isIntersecting) { x.target.classList.add('in'); io.unobserve(x.target); } }); }, { threshold: .08 });
    document.querySelectorAll('.reveal').forEach(function (el) { io.observe(el); });
  } else { $('.reveal').addClass('in'); }
})(jQuery);
