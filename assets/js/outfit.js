/*!
 * DRESSY outfit renderer - draws the dummy mannequin + selected garments as inline SVG.
 * Pure vector (no images), so colours/styles can be mixed freely.
 * Used by: customizer page, cart / order thumbnails, admin order view, product art generator (node).
 */
(function (root, factory) {
  if (typeof module === 'object' && module.exports) module.exports = factory();
  else root.Outfit = factory();
})(typeof self !== 'undefined' ? self : this, function () {
  'use strict';

  var SKIN = '#dcd9d3', SKIN_D = '#c9c5bd';

  function clamp(n) { return Math.max(0, Math.min(255, Math.round(n))); }
  function hex2rgb(h) {
    h = (h || '#111111').replace('#', '');
    if (h.length === 3) h = h.replace(/(.)/g, '$1$1');
    return [parseInt(h.substr(0, 2), 16), parseInt(h.substr(2, 2), 16), parseInt(h.substr(4, 2), 16)];
  }
  function shade(hex, pct) { // pct -100..100
    var c = hex2rgb(hex), t = pct < 0 ? 0 : 255, p = Math.abs(pct) / 100;
    return '#' + c.map(function (v) { return ('0' + clamp((t - v) * p + v).toString(16)).slice(-2); }).join('');
  }
  function lum(hex) { var c = hex2rgb(hex); return (0.299 * c[0] + 0.587 * c[1] + 0.114 * c[2]) / 255; }
  function detail(hex, amt) { return lum(hex) > 0.55 ? shade(hex, -(amt || 22)) : shade(hex, (amt || 22) + 6); }

  /* ---------------- mannequin ---------------- */
  function mannequin() {
    return '' +
      '<ellipse cx="150" cy="702" rx="86" ry="9" fill="#000" opacity=".09"/>' +
      // legs
      '<path d="M112 286 C106 380 112 470 118 560 L119 656 L147 656 L149 560 C152 470 150 380 150 330 Z" fill="' + SKIN + '"/>' +
      '<path d="M188 286 C194 380 188 470 182 560 L181 656 L153 656 L151 560 C148 470 150 380 150 330 Z" fill="' + SKIN_D + '"/>' +
      // feet
      '<path d="M119 650 L147 650 L151 678 Q151 686 142 686 L100 688 Q92 688 96 680 Q104 672 114 668 Z" fill="' + SKIN + '"/>' +
      '<path d="M181 650 L153 650 L149 678 Q149 686 158 686 L200 688 Q208 688 204 680 Q196 672 186 668 Z" fill="' + SKIN_D + '"/>' +
      // arms
      '<path d="M108 110 L82 124 L66 232 L58 298" stroke="' + SKIN + '" stroke-width="22" stroke-linecap="round" stroke-linejoin="round" fill="none"/>' +
      '<path d="M192 110 L218 124 L234 232 L242 298" stroke="' + SKIN_D + '" stroke-width="22" stroke-linecap="round" stroke-linejoin="round" fill="none"/>' +
      '<circle cx="57" cy="306" r="11" fill="' + SKIN + '"/><circle cx="243" cy="306" r="11" fill="' + SKIN_D + '"/>' +
      // torso + neck + head
      '<path d="M106 106 Q150 96 194 106 L190 192 Q186 244 194 290 L106 290 Q114 244 110 192 Z" fill="' + SKIN + '"/>' +
      '<path d="M150 100 Q186 100 194 106 L190 192 Q186 244 194 290 L150 290 Z" fill="' + SKIN_D + '" opacity=".55"/>' +
      '<rect x="139" y="80" width="22" height="26" rx="8" fill="' + SKIN + '"/>' +
      '<ellipse cx="150" cy="52" rx="25" ry="31" fill="' + SKIN + '"/>' +
      '<path d="M150 21 Q177 24 175 52 Q173 80 150 83 Z" fill="' + SKIN_D + '" opacity=".55"/>';
  }

  /* ---------------- tops ---------------- */
  var TEE = {
    crew: 'M128 98 Q150 116 172 98 L214 112 L238 170 L212 188 L192 160 L192 266 L108 266 L108 160 L88 188 L62 170 L86 112 Z',
    vneck: 'M126 98 L150 138 L174 98 L214 112 L238 170 L212 188 L192 160 L192 266 L108 266 L108 160 L88 188 L62 170 L86 112 Z',
    polo: 'M132 98 L150 112 L168 98 L214 112 L240 176 L214 192 L192 164 L192 270 L108 270 L108 164 L86 192 L60 176 L86 112 Z',
    oversized: 'M126 98 Q150 116 174 98 L224 118 L250 202 L220 214 L204 180 L206 292 L94 292 L96 180 L80 214 L50 202 L76 118 Z'
  };
  var SHIRT = {
    formal: 'M130 96 L150 112 L170 96 L212 110 L242 262 L216 270 L192 172 L192 280 L108 280 L108 172 L84 270 L58 262 L88 110 Z',
    casual: 'M130 96 L150 110 L170 96 L212 110 L234 172 L208 188 L192 162 L192 286 L108 286 L108 162 L92 188 L66 172 L88 110 Z'
  };

  function top(type, style, color) {
    var d = detail(color, 18), dk = shade(color, -14), g = '';
    if (type === 'tshirt') {
      var path = TEE[style] || TEE.crew;
      g += '<path d="' + path + '" fill="' + color + '" stroke="' + shade(color, -22) + '" stroke-width="1.2" stroke-linejoin="round"/>';
      if (style === 'crew' || style === 'oversized') g += '<path d="M128 98 Q150 118 172 98" fill="none" stroke="' + d + '" stroke-width="5" stroke-linecap="round"/>';
      if (style === 'vneck') g += '<path d="M126 98 L150 138 L174 98" fill="none" stroke="' + d + '" stroke-width="4" stroke-linejoin="round"/>';
      if (style === 'polo') {
        g += '<path d="M126 96 L150 116 L145 130 L118 104 Z" fill="' + dk + '" stroke="' + shade(color, -28) + '" stroke-width="1"/>' +
          '<path d="M174 96 L150 116 L155 130 L182 104 Z" fill="' + dk + '" stroke="' + shade(color, -28) + '" stroke-width="1"/>' +
          '<path d="M150 116 L150 160" stroke="' + d + '" stroke-width="1.6"/><circle cx="150" cy="130" r="2.2" fill="' + d + '"/><circle cx="150" cy="146" r="2.2" fill="' + d + '"/>' +
          '<path d="M62 176 L87 190 M238 176 L213 190" stroke="' + d + '" stroke-width="4"/>';
      }
      g += '<path d="M108 262 L192 262" stroke="' + shade(color, -10) + '" stroke-width="1" stroke-dasharray="3 3" opacity=".7"/>';
    } else { // shirt
      var sp = SHIRT[style] || SHIRT.formal;
      g += '<path d="' + sp + '" fill="' + color + '" stroke="' + shade(color, -22) + '" stroke-width="1.2" stroke-linejoin="round"/>';
      g += '<path d="M126 94 L150 118 L144 132 L116 104 Z" fill="' + shade(color, lum(color) > .55 ? -6 : 10) + '" stroke="' + shade(color, -30) + '" stroke-width="1"/>' +
        '<path d="M174 94 L150 118 L156 132 L184 104 Z" fill="' + shade(color, lum(color) > .55 ? -6 : 10) + '" stroke="' + shade(color, -30) + '" stroke-width="1"/>';
      g += '<path d="M150 118 L150 ' + (style === 'formal' ? 276 : 282) + '" stroke="' + d + '" stroke-width="1.4"/>';
      for (var y = 142; y < 270; y += 30) g += '<circle cx="150" cy="' + y + '" r="2.3" fill="' + d + '"/>';
      g += '<rect x="116" y="150" width="22" height="26" rx="2" fill="none" stroke="' + d + '" stroke-width="1.2"/>';
      if (style === 'formal') g += '<path d="M62 254 L86 262 M238 254 L214 262" stroke="' + d + '" stroke-width="5" stroke-linecap="round"/>';
      else g += '<path d="M66 176 L90 190 M234 176 L210 190" stroke="' + d + '" stroke-width="4" stroke-linecap="round"/>';
    }
    return g;
  }

  /* ---------------- jeans ---------------- */
  var JEANS = {
    slim: 'M106 258 L194 258 L196 300 L186 652 L154 652 L150 338 L146 652 L114 652 L104 300 Z',
    straight: 'M106 258 L194 258 L197 300 L191 652 L154 652 L150 338 L146 652 L109 652 L103 300 Z',
    bootcut: 'M106 258 L194 258 L197 300 L192 560 L204 654 L154 654 L150 338 L146 654 L96 654 L108 560 L103 300 Z',
    shorts: 'M106 258 L194 258 L198 300 L196 446 L154 446 L150 346 L146 446 L104 446 L102 300 Z',
    jogger: 'M104 258 L196 258 L201 312 L192 520 L187 616 L156 616 L150 340 L144 616 L113 616 L108 520 L99 312 Z'
  };

  function jeans(style, color) {
    var p = JEANS[style] || JEANS.slim, d = detail(color, 20), g = '';
    g += '<path d="' + p + '" fill="' + color + '" stroke="' + shade(color, -26) + '" stroke-width="1.2" stroke-linejoin="round"/>';
    // waistband + loops
    g += '<path d="M105 258 L195 258 L196 274 L104 274 Z" fill="' + shade(color, -8) + '" stroke="' + shade(color, -26) + '" stroke-width="1"/>';
    [118, 134, 166, 182].forEach(function (x) { g += '<rect x="' + (x - 2) + '" y="256" width="4" height="22" rx="1" fill="' + d + '" opacity=".8"/>'; });
    // fly + pockets + stitching
    g += '<path d="M150 274 L150 338" stroke="' + d + '" stroke-width="1.4"/><path d="M150 286 Q140 300 141 318" stroke="' + d + '" stroke-width="1.2" fill="none" stroke-dasharray="3 2"/>';
    g += '<path d="M106 278 Q124 296 128 322" fill="none" stroke="' + d + '" stroke-width="1.4"/><path d="M194 278 Q176 296 172 322" fill="none" stroke="' + d + '" stroke-width="1.4"/>';
    g += '<circle cx="150" cy="282" r="3" fill="' + d + '"/>';
    if (style === 'jogger') {
      g += '<rect x="112" y="600" width="46" height="16" rx="3" fill="' + shade(color, -12) + '"/><rect x="142" y="600" width="46" height="16" rx="3" fill="' + shade(color, -12) + '"/>';
      g += '<rect x="110" y="360" width="26" height="34" rx="3" fill="none" stroke="' + d + '" stroke-width="1.3"/><rect x="164" y="360" width="26" height="34" rx="3" fill="none" stroke="' + d + '" stroke-width="1.3"/>';
    } else if (style === 'shorts') {
      g += '<path d="M104 436 L146 436 M154 436 L196 436" stroke="' + d + '" stroke-width="1.2" stroke-dasharray="3 3"/>';
    } else {
      g += '<path d="M116 640 L146 640 M154 640 L186 640" stroke="' + d + '" stroke-width="1.2" stroke-dasharray="3 3"/>';
      g += '<path d="M112 420 Q128 440 122 470" stroke="' + shade(color, lum(color) > .5 ? -10 : 12) + '" stroke-width="10" opacity=".25" fill="none" stroke-linecap="round"/>';
    }
    return g;
  }

  /* ---------------- shoes (left shoe, mirrored for the right) ---------------- */
  function shoeLeft(style, color) {
    var d = detail(color, 22), sole = style === 'boot' || style === 'loafer' ? '#2a2a2a' : '#f3f3f1', g = '';
    if (style === 'sandal') {
      g += '<path d="M90 682 L150 680 L150 690 L86 692 Q80 692 82 687 Z" fill="' + color + '"/>' +
        '<path d="M114 664 L140 680 M142 662 L116 678" stroke="' + color + '" stroke-width="5" stroke-linecap="round" fill="none"/>' +
        '<path d="M116 660 L146 660" stroke="' + color + '" stroke-width="6" stroke-linecap="round"/>';
      return g;
    }
    var h = style === 'boot' ? 606 : 652;
    g += '<path d="M115 ' + h + ' L150 ' + h + ' L151 672 Q151 686 138 688 L98 690 Q88 690 90 682 Q92 674 104 672 L112 668 Z" fill="' + color + '" stroke="' + shade(color, -28) + '" stroke-width="1.2" stroke-linejoin="round"/>';
    g += '<path d="M88 686 L151 684 L151 693 L86 695 Q80 695 82 690 Z" fill="' + sole + '" stroke="' + shade(sole, -25) + '" stroke-width="1"/>';
    if (style === 'sneaker') {
      g += '<path d="M120 660 L142 656 M120 666 L144 662" stroke="#fff" stroke-width="2.2" stroke-linecap="round"/><path d="M94 680 Q100 672 112 672" fill="none" stroke="' + d + '" stroke-width="2"/>';
    } else if (style === 'boot') {
      g += '<path d="M115 616 L150 616" stroke="' + d + '" stroke-width="3"/><path d="M122 628 L144 628 M122 640 L144 640 M122 652 L144 652" stroke="' + d + '" stroke-width="2" stroke-linecap="round"/>';
    } else if (style === 'loafer') {
      g += '<path d="M116 662 Q132 670 150 662" fill="none" stroke="' + d + '" stroke-width="3"/><rect x="127" y="662" width="10" height="5" rx="1" fill="' + d + '"/>';
    }
    return g;
  }
  function shoes(style, color) {
    return '<g>' + shoeLeft(style, color) + '</g><g transform="translate(300 0) scale(-1 1)">' + shoeLeft(style, color) + '</g>';
  }

  /* ---------------- public ---------------- */
  /** sel = { jeans:{style,color}, top:{type:'tshirt'|'shirt',style,color}, shoes:{style,color} } (every key optional) */
  function svg(sel, opts) {
    opts = opts || {};
    sel = sel || {};
    var body = mannequin();
    if (sel.jeans) body += jeans(sel.jeans.style, sel.jeans.color);
    if (sel.shoes) body += shoes(sel.shoes.style, sel.shoes.color);
    if (sel.top) body += top(sel.top.type, sel.top.style, sel.top.color);
    var vb = opts.crop || '0 0 300 720';
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="' + vb + '" class="outfit-svg" role="img" aria-label="Outfit preview">' + body + '</svg>';
  }

  /** normalise the server "outfit" object (slot => {type,style,color}) */
  function normalize(o) {
    var s = {};
    if (!o) return s;
    if (o.jeans) s.jeans = o.jeans;
    if (o.shoes) s.shoes = o.shoes;
    if (o.top) s.top = o.top;
    return s;
  }

  function hydrate(ctx) {
    if (typeof document === 'undefined') return;
    (ctx || document).querySelectorAll('.outfit-thumb[data-outfit]').forEach(function (el) {
      if (el.getAttribute('data-ready')) return;
      try { el.innerHTML = svg(normalize(JSON.parse(el.getAttribute('data-outfit')))); el.setAttribute('data-ready', '1'); } catch (e) { }
    });
  }
  if (typeof document !== 'undefined') {
    if (document.readyState !== 'loading') hydrate(); else document.addEventListener('DOMContentLoaded', function () { hydrate(); });
  }

  return { svg: svg, hydrate: hydrate, shade: shade, lum: lum, detail: detail, parts: { top: top, jeans: jeans, shoes: shoes, mannequin: mannequin } };
});
