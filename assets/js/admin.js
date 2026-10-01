(function ($) {
  'use strict';
  var A = window.ADMIN || {};
  function cookie(n) { var m = document.cookie.match(new RegExp('(?:^|; )' + n + '=([^;]*)')); return m ? decodeURIComponent(m[1]) : ''; }
  $.ajaxPrefilter(function (opt) {
    if (opt.type && opt.type.toUpperCase() === 'POST' && !opt.crossDomain) {
      var tok = A.csrfHash || cookie(A.csrfCookie);
      if (typeof opt.data === 'string') opt.data += (opt.data ? '&' : '') + encodeURIComponent(A.csrfName) + '=' + encodeURIComponent(tok);
      else { opt.data = $.extend({}, opt.data); opt.data[A.csrfName] = tok; opt.data = $.param(opt.data); }
    }
  });
  $('.a-burger').on('click', function () { $('body').toggleClass('side-open'); });
  $(document).on('click', function (e) { if ($('body').hasClass('side-open') && !$(e.target).closest('.a-side,.a-burger').length) $('body').removeClass('side-open'); });

  // AJAX toggle switches in lists
  $(document).on('change', '.sw[data-url] input', function () {
    var $i = $(this), url = $i.closest('.sw').data('url');
    $.post(url, {}, function (r) { if (!r.ok) $i.prop('checked', !$i.prop('checked')); }, 'json').fail(function () { $i.prop('checked', !$i.prop('checked')); alert('Could not update. Please refresh and try again.'); });
  });
  // delete confirm
  $(document).on('submit', 'form[data-confirm]', function (e) { if (!window.confirm($(this).data('confirm'))) e.preventDefault(); });
  // image preview
  $(document).on('change', 'input[type=file][data-prev]', function () {
    var f = this.files[0], $p = $($(this).data('prev'));
    if (f && $p.length) { var r = new FileReader(); r.onload = function (ev) { $p.attr('src', ev.target.result).show(); }; r.readAsDataURL(f); }
  });
  // colour input sync
  $(document).on('input', 'input[type=color]', function () { $(this).next('.hexv').text(this.value); });
  // copy
  $(document).on('click', '[data-copy]', function () {
    var t = $($(this).data('copy')).text(); var $b = $(this);
    (navigator.clipboard ? navigator.clipboard.writeText(t) : Promise.resolve()).then(function () { var o = $b.text(); $b.text('Copied'); setTimeout(function () { $b.text(o); }, 1200); });
  });
  if (window.Outfit) Outfit.hydrate();
})(jQuery);
