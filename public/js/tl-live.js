/* ===== pemuat desain global: memuat tl-ui.css, tl-ui.js, tl-report.js ===== */

(function () {

  try {

    var lk = document.querySelector('link[href*="tl-live.css"]');

    if (!lk) { return; }

    var base = lk.href.split('/css/tl-live.css')[0], v = '39';

    if (!document.querySelector('link[href*="tl-ui.css"]')) {

      var l = document.createElement('link'); l.rel = 'stylesheet'; l.href = base + '/css/tl-ui.css?v=' + v; document.head.appendChild(l);

    }

    ['tl-ui.js', 'tl-report.js'].forEach(function (f) {

      if (document.querySelector('script[src*="' + f + '"]')) { return; }

      var s = document.createElement('script'); s.src = base + '/js/' + f + '?v=' + v; document.body.appendChild(s);

    });

  } catch (e) { }

})();



/* TRAFIE-LIBRARY : tema, mode gelap, lonceng, dan Lumi AI. Mengenali elemen lewat tulisannya. */

(function () {

  'use strict';

  function ready(fn) { if (document.readyState !== 'loading') fn(); else document.addEventListener('DOMContentLoaded', fn); }

  function safe(fn) { try { fn(); } catch (e) { if (window.console) console.warn('[tl-live]', e); } }

  function px(v) { var n = parseFloat(v); return isNaN(n) ? 0 : n; }

  function el(tag, cls, text) { var e = document.createElement(tag); if (cls) { e.className = cls; } if (text !== undefined) { e.textContent = text; } return e; }

  function ls(k, v) { try { if (v === undefined) { return localStorage.getItem(k); } localStorage.setItem(k, v); } catch (e) { return null; } }



  var css = document.querySelector('link[href*="tl-live.css"]');

  var BASE = css ? css.href.split('/css/tl-live.css')[0] : '';

  var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';



  var THEMES = [

    { id: 'pastel', name: 'Pastel Dream', hue: 0, sat: 1, c: 'linear-gradient(135deg,#d9ccf5,#f7c8dc)' },

    { id: 'sakura', name: 'Sakura Bloom', hue: -32, sat: 1.06, c: 'linear-gradient(135deg,#f7b6cf,#fbd9a8)' },

    { id: 'ocean', name: 'Ocean Mist', hue: 135, sat: 1, c: 'linear-gradient(135deg,#a8d8f0,#b9e6d6)' },

    { id: 'mint', name: 'Mint Garden', hue: 85, sat: 1.05, c: 'linear-gradient(135deg,#b9e6c9,#e3f0a8)' },

    { id: 'lilac', name: 'Lilac Night', hue: 28, sat: .95, c: 'linear-gradient(135deg,#b9a8ee,#8f9be8)' }

  ];

  var state = { theme: ls('tl_theme') || 'pastel', dark: ls('tl_dark') === '1' };



  function applyLook() {

    var t = THEMES.filter(function (x) { return x.id === state.theme; })[0] || THEMES[0];

    var f = 'hue-rotate(' + t.hue + 'deg) saturate(' + t.sat + ')';

    if (state.dark) { f += ' invert(.9) hue-rotate(180deg)'; }

    document.documentElement.style.filter = (t.hue === 0 && t.sat === 1 && !state.dark) ? '' : f;

    document.documentElement.classList.toggle('tl-dark', state.dark);

  }

  applyLook();



  function findText(txt, starts) {

    var w = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null), n, t0 = txt.toLowerCase();

    var res = [];

    while ((n = w.nextNode())) {

      var p = n.parentElement;

      if (!p || /^(SCRIPT|STYLE|NOSCRIPT)$/.test(p.tagName)) { continue; }

      var t = n.nodeValue.replace(/\s+/g, ' ').trim().toLowerCase();

      if (t && (starts ? t.indexOf(t0) === 0 : t === t0)) { res.push(n); }

    }

    return res;

  }



  var openPanel = null;

  function closePanel() { if (openPanel) { openPanel.remove(); openPanel = null; } }

  document.addEventListener('click', function (e) { if (openPanel && !openPanel.contains(e.target) && !e.target.closest('[data-tll]')) { closePanel(); } });

  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { closePanel(); } });

  function showPanel(anchor, panel) {

    closePanel();

    document.body.appendChild(panel);

    var r = anchor.getBoundingClientRect();

    panel.style.top = (r.bottom + window.scrollY + 8) + 'px';

    panel.style.right = Math.max(8, document.documentElement.clientWidth - r.right - window.scrollX) + 'px';

    openPanel = panel;

  }



  /* ---------- tema (Pastel Dream) ---------- */

  function setPillName(textNode, name) { if (textNode) { textNode.nodeValue = textNode.nodeValue.replace(/Pastel Dream|Sakura Bloom|Ocean Mist|Mint Garden|Lilac Night/, name); } }

  function setupTheme(pill, textNode) {

    var cur = THEMES.filter(function (x) { return x.id === state.theme; })[0] || THEMES[0];

    setPillName(textNode, cur.name);

    pill.style.cursor = 'pointer'; pill.setAttribute('data-tll', '1');

    pill.addEventListener('click', function (e) {

      e.preventDefault(); e.stopPropagation();

      if (openPanel && openPanel.getAttribute('data-k') === 'theme') { closePanel(); return; }

      var p = el('div', 'tll-panel'); p.setAttribute('data-k', 'theme');

      p.appendChild(el('div', 'tll-h', 'Pilih tema'));

      THEMES.forEach(function (t) {

        var b = el('button', 'tll-opt' + (t.id === state.theme ? ' on' : '')); b.type = 'button';

        var sw = el('span', 'tll-sw'); sw.style.background = t.c; b.appendChild(sw); b.appendChild(document.createTextNode(t.name));

        b.addEventListener('click', function () { state.theme = t.id; ls('tl_theme', t.id); applyLook(); setPillName(textNode, t.name); closePanel(); });

        p.appendChild(b);

      });

      showPanel(pill, p);

    });

  }



  /* ---------- mode gelap (ikon matahari) ---------- */

  var SUN = '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>';

  var MOON = '<path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/>';

  function setupSun(btn) {

    btn.style.cursor = 'pointer'; btn.setAttribute('data-tll', '1'); btn.title = 'Mode terang / gelap';

    function icon() { var s = btn.querySelector('svg'); if (s) { s.innerHTML = state.dark ? MOON : SUN; } }

    icon();

    btn.addEventListener('click', function (e) { e.preventDefault(); e.stopPropagation(); state.dark = !state.dark; ls('tl_dark', state.dark ? '1' : '0'); applyLook(); icon(); });

  }



  /* ---------- lonceng ---------- */

  function setupBell(btn) {

    btn.style.cursor = 'pointer'; btn.setAttribute('data-tll', '1');

    if (getComputedStyle(btn).position === 'static') { btn.style.position = 'relative'; }

    var badge = el('span', 'tll-badge'); badge.hidden = true; btn.appendChild(badge);

    function load(cb) {

      fetch(BASE + '/tl-live/notifs', { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })

        .then(function (r) { return r.json(); })

        .then(function (j) { badge.textContent = j.count > 9 ? '9+' : j.count; badge.hidden = !j.count; if (cb) { cb(j); } })

        .catch(function () { if (cb) { cb({ items: [] }); } });

    }

    load();

    btn.addEventListener('click', function (e) {

      e.preventDefault(); e.stopPropagation();

      if (openPanel && openPanel.getAttribute('data-k') === 'bell') { closePanel(); return; }

      var p = el('div', 'tll-panel'); p.setAttribute('data-k', 'bell'); p.style.width = '320px';

      p.appendChild(el('div', 'tll-h', 'Notifikasi'));

      p.appendChild(el('div', 'tll-empty', 'Memuat...'));

      showPanel(btn, p);

      load(function (j) {

        while (p.childNodes.length > 1) { p.removeChild(p.lastChild); }

        if (!j.items || !j.items.length) { p.appendChild(el('div', 'tll-empty', 'Belum ada notifikasi baru.')); return; }

        j.items.forEach(function (it) {

          var d = el('div', 'tll-nt ' + (it.level || '')); d.appendChild(el('b', '', it.title)); d.appendChild(el('small', '', it.body || ''));

          p.appendChild(d);

        });

      });

    });

  }



  /* ---------- Lumi AI ---------- */

  var chat = null;

  function buildChat() {

    if (chat) { return chat; }

    var fab = el('button', '', ''); fab.id = 'tllFab'; fab.type = 'button'; fab.title = 'Tanya Lumi';

    fab.innerHTML = '<svg viewBox="0 0 24 24"><path d="M21 12a8 8 0 0 1-11.6 7.1L3 21l1.9-5.4A8 8 0 1 1 21 12z"/><path d="M9 11h.01M15 11h.01"/></svg>';

    var box = el('div', ''); box.id = 'tllChat';

    var top = el('div', 'tlc-top'); var av = el('span', 'av', '\u2728');

    var tt = el('div'); tt.appendChild(el('b', '', 'Lumi AI')); tt.appendChild(el('small', '', 'Your Reading Companion'));

    var x = el('button', '', '\u00d7'); x.type = 'button'; x.setAttribute('aria-label', 'Tutup');

    top.appendChild(av); top.appendChild(tt); top.appendChild(x);

    var body = el('div', 'tlc-body'); var chips = el('div', 'tlc-chips');

    var form = el('form', 'tlc-form'); var inp = el('input'); inp.type = 'text'; inp.placeholder = 'Tanya Lumi...'; inp.maxLength = 200; inp.autocomplete = 'off';

    var send = el('button'); send.type = 'submit'; send.innerHTML = '<svg viewBox="0 0 24 24"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4z"/></svg>';

    form.appendChild(inp); form.appendChild(send);

    box.appendChild(top); box.appendChild(body); box.appendChild(chips); box.appendChild(form);

    document.body.appendChild(fab); document.body.appendChild(box);



    function msg(text, who, books) {

      var m = el('div', 'tlc-m ' + who); m.appendChild(document.createTextNode(text));

      (books || []).forEach(function (b) {

        var a = el('a', 'tlc-bk'); a.href = b.url || '#';

        if (b.cover) { var im = el('img'); im.src = b.cover; im.alt = ''; a.appendChild(im); } else { a.appendChild(el('span', 'ph')); }

        var d = el('div'); d.appendChild(el('b', '', b.title)); d.appendChild(el('small', '', (b.author || '') + (b.category ? ' \u00b7 ' + b.category : ''))); a.appendChild(d); m.appendChild(a);

      });

      body.appendChild(m); body.scrollTop = body.scrollHeight;

    }

    function setChips(list) {

      chips.textContent = '';

      (list || []).forEach(function (c) { var b = el('button', '', c); b.type = 'button'; b.addEventListener('click', function () { ask(c); }); chips.appendChild(b); });

    }

    function ask(q) {

      q = (q || '').trim(); if (!q) { return; }

      msg(q, 'me'); inp.value = '';

      var ty = el('div', 'tlc-typing'); ty.innerHTML = '<i></i><i></i><i></i>'; body.appendChild(ty); body.scrollTop = body.scrollHeight;

      fetch(BASE + '/tl-live/lumi', { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: JSON.stringify({ q: q }) })

        .then(function (r) { return r.json(); })

        .then(function (j) { ty.remove(); msg(j.reply || 'Maaf, aku belum bisa menjawab itu.', 'bot', j.books); setChips(j.chips); })

        .catch(function () { ty.remove(); msg('Maaf, koneksi bermasalah. Coba lagi ya.', 'bot'); });

    }

    form.addEventListener('submit', function (e) { e.preventDefault(); ask(inp.value); });

    function open() { box.classList.add('open'); if (!body.childNodes.length) { msg('Halo! Aku Lumi \u2728 Aku bisa mencarikan buku, mengecek pinjaman, dan menjawab soal denda. Mau tanya apa?', 'bot'); setChips(['Rekomendasi buku seru', 'Buku yang sedang aku pinjam', 'Denda saya', 'Cara meminjam buku']); } setTimeout(function () { inp.focus(); }, 60); }

    function close() { box.classList.remove('open'); }

    fab.addEventListener('click', function () { if (box.classList.contains('open')) { close(); } else { open(); } });

    x.addEventListener('click', close);

    chat = { open: open };

    return chat;

  }



  function hookLumiCard() {

    findText('Lumi segera hadir', true).forEach(function (n) {

      n.nodeValue = 'Tanya Lumi...';

      var p = n.parentElement; p.style.cursor = 'pointer';

      p.addEventListener('click', function () { buildChat().open(); });

    });

    ['Chat with Lumi', 'Mulai Chat'].forEach(function (t) {

      findText(t, true).forEach(function (n) {

        var p = n.parentElement.closest('a, button, span, div') || n.parentElement; p.style.cursor = 'pointer';

        p.addEventListener('click', function (e) { e.preventDefault(); buildChat().open(); });

      });

    });

    [].forEach.call(document.querySelectorAll('input[placeholder*="Lumi"]'), function (i) {

      i.readOnly = true; i.style.cursor = 'pointer'; i.addEventListener('click', function () { buildChat().open(); });

    });

  }



  ready(function () {

    safe(buildChat);

    safe(hookLumiCard);

    safe(function () {

      var nodes = findText('Pastel Dream'); if (!nodes.length) { nodes = findText('Sakura Bloom').concat(findText('Ocean Mist'), findText('Mint Garden'), findText('Lilac Night')); }

      if (!nodes.length) { return; }

      var tn = nodes[0], a = tn.parentElement, pill = a;

      for (var i = 0; i < 4 && a && a.parentElement; i++) {

        var r = a.getBoundingClientRect();

        if (r.width > 90 && r.height < 64 && px(getComputedStyle(a).borderTopLeftRadius) >= 12) { pill = a; break; }

        a = a.parentElement;

      }

      setupTheme(pill, tn);

      var sun = pill.nextElementSibling, bell = sun ? sun.nextElementSibling : null;

      if (sun && sun.querySelector('svg')) { setupSun(sun); }

      if (bell && bell.querySelector('svg')) { setupBell(bell); }

    });

  });

})();