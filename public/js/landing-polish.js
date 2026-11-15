/* TRAFIE-LIBRARY : LANDING POLISH (V2-30)
   Mengenali elemen lewat tulisannya, jadi tidak bergantung pada nama class. */
(function () {
  'use strict';
  function ready(fn) { if (document.readyState !== 'loading') fn(); else document.addEventListener('DOMContentLoaded', fn); }
  function px(v) { var n = parseFloat(v); return isNaN(n) ? 0 : n; }
  function norm(s) { return (s || '').replace(/\s+/g, ' ').trim().toLowerCase(); }
  function safe(fn) { try { fn(); } catch (e) { if (window.console) console.warn('[landing-polish]', e); } }

  // elemen terdalam yang teksnya persis (atau diawali) $txt
  function byText(txt, starts) {
    var res = [], t0 = norm(txt);
    var w = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null);
    var n;
    while ((n = w.nextNode())) {
      var p = n.parentElement;
      if (!p || /^(SCRIPT|STYLE|NOSCRIPT)$/.test(p.tagName)) continue;
      var t = norm(n.nodeValue);
      if (!t) continue;
      if (starts ? t.indexOf(t0) === 0 : t === t0) res.push(p);
    }
    return res;
  }
  function transparentBg(c) { return c === 'transparent' || /rgba\(\s*0,\s*0,\s*0,\s*0\s*\)/.test(c); }
  // naik dari elemen sampai ketemu "kartu" (sudut membulat + ada kotak/bayangan)
  function cardOf(el, minW, minR) {
    var a = el;
    for (var i = 0; i < 10 && a && a !== document.body; i++) {
      var cs = getComputedStyle(a), r = px(cs.borderTopLeftRadius), w = a.getBoundingClientRect().width;
      var box = cs.boxShadow !== 'none' || px(cs.borderTopWidth) > 0 || cs.backgroundImage !== 'none' || !transparentBg(cs.backgroundColor);
      if (r >= (minR || 14) && box && w >= (minW || 220)) return a;
      a = a.parentElement;
    }
    return null;
  }

  // pasang kerangka kosong; lebar mengikuti ruang yang tersedia
  function mount(el, kind, html) {
    if (el.closest('.tlp-empty')) return;
    var host = el.parentNode;
    var wrap = document.createElement('div'); wrap.className = 'tlp-empty';
    var g = document.createElement('div'); g.className = kind; g.innerHTML = html;
    host.classList.add('tlp-empty-host');
    host.insertBefore(wrap, el); wrap.appendChild(g); wrap.appendChild(el);
    function fit() {
      var a = host, w = 0;
      while (a && a !== document.body) {
        var cs = getComputedStyle(a), inner = a.clientWidth - px(cs.paddingLeft) - px(cs.paddingRight);
        if (inner >= 260) { w = inner; break; }
        a = a.parentElement;
      }
      wrap.style.width = (w || 420) + 'px'; wrap.style.maxWidth = '100%';
    }
    fit(); window.addEventListener('resize', fit);
  }

  ready(function () {
    if (!/discover your next/i.test(document.body.textContent || '')) return;
    document.documentElement.classList.add('tlp');

    // latar warna + progres scroll
    safe(function () {
      var bg = document.createElement('div'); bg.className = 'tlp-bg'; document.body.appendChild(bg);
      var bar = document.createElement('div'); bar.className = 'tlp-progress'; document.body.appendChild(bar);
      var tick = false;
      function upd() {
        var h = document.documentElement.scrollHeight - window.innerHeight;
        bar.style.transform = 'scaleX(' + (h > 0 ? Math.min(1, window.scrollY / h) : 0) + ')';
        tick = false;
      }
      window.addEventListener('scroll', function () { if (!tick) { tick = true; requestAnimationFrame(upd); } }, { passive: true });
      upd();
    });

    // ---- kerangka kosong: Buku Pilihan & Ulasan ----
    safe(function () {
      byText('Belum ada buku di katalog', true).forEach(function (el) {
        mount(el, 'tlp-ghost', new Array(5).join('<span class="tlp-gt"><span class="tlp-gc"></span><span class="tlp-gl"></span><span class="tlp-gl s"></span></span>'));
      });
      byText('Belum ada ulasan', true).forEach(function (el) {
        mount(el, 'tlp-gq', new Array(4).join('<div class="tlp-gqc"><div class="q">&ldquo;</div><span class="tlp-gl"></span><span class="tlp-gl s"></span><div class="who"><span class="av"></span><span class="tlp-gl"></span></div></div>'));
      });
    });

    // ---- kartu utama ----
    var cards = [];
    function glass(card) {
      if (!card || card.classList.contains('tlp-card')) return;
      card.classList.add('tlp-card');
      if (getComputedStyle(card).position === 'static') card.style.position = 'relative';
      var b = getComputedStyle(card, '::before').content, a = getComputedStyle(card, '::after').content;
      if (!b || b === 'none' || b === 'normal') card.classList.add('tlp-b');
      if (!a || a === 'none' || a === 'normal') card.classList.add('tlp-a');
      cards.push(card);
    }
    safe(function () {
      ['Buku Pilihan', 'Kategori Populer', 'Statistik Perpustakaan', 'Apa Kata Mereka?', 'Reading Challenge', 'Lumi AI'].forEach(function (t) {
        byText(t).forEach(function (el) { glass(cardOf(el, 220, 14)); });
      });
    });

    // ---- Reading Challenge & Lumi: teks tidak menabrak gambar + gambar melayang ----
    safe(function () {
      byText('Reading Challenge').forEach(function (h) {
        var card = h.closest('.tlp-card'); if (!card) return;
        var box = h.parentElement;
        while (box && box !== card && box.getBoundingClientRect().width > card.getBoundingClientRect().width * 0.8) box = box.parentElement;
        if (box && box !== card) box.classList.add('tlp-rc-text');
        else if (h.parentElement && h.parentElement !== card) h.parentElement.classList.add('tlp-rc-text');
        [].forEach.call(card.querySelectorAll('img, svg'), function (m) { var r = m.getBoundingClientRect(); if (r.width > 60 && r.height > 60) m.classList.add('tlp-float'); });
      });
      byText('Lumi AI').forEach(function (h) {
        var card = h.closest('.tlp-card'); if (!card) return;
        [].forEach.call(card.querySelectorAll('img'), function (m) { var r = m.getBoundingClientRect(); if (r.width > 60 && r.height > 60) m.classList.add('tlp-float'); });
      });
    });

    // ---- statistik ----
    safe(function () {
      ['Total Buku', 'Siswa Aktif', 'Peminjaman Bulanan', 'Tingkat Kepuasan'].forEach(function (t) {
        byText(t).forEach(function (lab) {
          var num = lab.previousElementSibling;
          if (num) num.classList.add('tlp-num');
          var tile = cardOf(lab, 100, 10);
          if (tile && !tile.classList.contains('tlp-card')) tile.classList.add('tlp-tile');
        });
      });
    });

    // ---- kategori ----
    safe(function () {
      ['Fiksi', 'Romance', 'Fantasy', 'Non-Fiksi', 'Mystery', 'Thriller', 'Sci-Fi', 'Self Development'].forEach(function (t) {
        byText(t).forEach(function (lab) {
          if (lab.closest('nav, header, footer')) return;
          var tile = cardOf(lab, 50, 10);
          if (!tile || tile.classList.contains('tlp-card')) return;
          if (tile.getBoundingClientRect().width > 200) return;
          lab.classList.add('tlp-catl'); tile.classList.add('tlp-cat');
        });
      });
    });

    // ---- hero ----
    safe(function () {
      var h = byText('Discover Your Next', true)[0]; if (!h) return;
      var a = h;
      while (a && a !== document.body) {
        var r = a.getBoundingClientRect();
        if (r.width >= window.innerWidth * 0.88 && r.height >= 360 && r.height < 1100) { a.classList.add('tlp-hero'); break; }
        a = a.parentElement;
      }
      byText('More Books', true).forEach(function (m) { m.classList.add('tlp-moreb'); });
    });

    // ---- footer ----
    safe(function () {
      var f = byText('Hubungi Kami')[0]; if (!f) return;
      var foot = f.closest('footer');
      if (!foot) {
        var a = f;
        while (a && a !== document.body) { if (a.getBoundingClientRect().width >= window.innerWidth * 0.9 && a.getBoundingClientRect().height < 700) { foot = a; } a = a.parentElement; }
      }
      if (foot) foot.classList.add('tlp-footer');
      byText('Where Every Book Blooms Into a Universe').forEach(function (t) {
        if (!t.closest('footer, .tlp-footer')) return;
        t.classList.add('tlp-tag'); if (t.parentElement) t.parentElement.classList.add('tlp-tag-p');
      });
      var ik = byText('Ikut Kami')[0];
      if (ik && ik.parentElement) {
        var seen = {};
        [].slice.call(ik.parentElement.querySelectorAll('a')).forEach(function (a) {
          var key = a.innerHTML.replace(/\s+/g, '');
          if (key.length < 20) return;
          if (seen[key]) a.remove(); else seen[key] = 1;
        });
      }
    });

    // ---- muncul halus ----
    safe(function () {
      if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) { cards.forEach(function (c) { c.classList.add('tlp-in'); }); return; }
      var io = new IntersectionObserver(function (es) {
        es.forEach(function (e) { if (e.isIntersecting) { e.target.classList.add('tlp-in'); io.unobserve(e.target); } });
      }, { threshold: 0.08 });
      cards.forEach(function (c, i) { c.style.transitionDelay = (i % 3) * 90 + 'ms'; c.classList.add('tlp-rv'); io.observe(c); });
      setTimeout(function () { cards.forEach(function (c) { c.classList.add('tlp-in'); }); }, 3000);
    });
  });
})();