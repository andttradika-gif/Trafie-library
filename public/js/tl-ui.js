/* TRAFIE-LIBRARY : penata UI global (mengenali elemen dari struktur, bukan nama class) */
(function () {
  'use strict';
  function ready(fn) { if (document.readyState !== 'loading') fn(); else document.addEventListener('DOMContentLoaded', fn); }
  function safe(fn) { try { fn(); } catch (e) { if (window.console) console.warn('[tl-ui]', e); } }
  function px(v) { var n = parseFloat(v); return isNaN(n) ? 0 : n; }
  function el(tag, cls) { var e = document.createElement(tag); if (cls) { e.className = cls; } return e; }

  var HERO = {
    'kategori buku': ['Kelola kategori untuk merapikan koleksi bukumu.', '<path d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z"/>'],
    'daftar siswa': ['Data siswa terdaftar, riwayat pinjam, dan reset password.', '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/>'],
    'peminjaman': ['Pantau pengajuan, buku yang dipinjam, dan pengembalian.', '<path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>'],
    'reservasi & waiting list': ['Antrean siswa untuk buku yang sedang dipinjam.', '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>'],
    'perpanjangan pinjaman': ['Setujui atau tolak permintaan perpanjangan.', '<path d="M21 12a9 9 0 0 0-15.5-6.2L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 15.5 6.2L21 16"/><path d="M21 21v-5h-5"/>'],
    'laporan peminjaman': ['Rekap peminjaman per periode, siap diunduh sebagai PDF.', '<path d="M3 3v18h18"/><path d="M7 15l4-4 3 3 5-6"/>'],
    'manajemen buku': ['Tambah, ubah, dan atur koleksi buku.', '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>'],
    'ulasan': ['Moderasi ulasan dan rating dari siswa.', '<path d="M12 2l3 6.5 7 .9-5.1 4.8 1.3 7L12 17.8 5.8 21.2l1.3-7L2 9.4l7-.9z"/>']
  };
  var BADGES = [[/^dipinjam$|^diajukan$|^dalam antrean$|^menunggu$/i, 'blue'], [/dikembalikan|selesai|lunas|disetujui|siap diambil|tersedia|aktif/i, 'green'],
    [/pending|menunggu keputusan/i, 'amber'], [/ditolak|dibatalkan|batal/i, 'pink'], [/terlambat|kedaluwarsa|belum dibayar|kadaluarsa/i, 'red'], [/^\d+ buku$/i, 'lav']];

  var ICONS = [
    [/edit|ubah/i, '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>'],
    [/hapus|delete/i, '<path d="M3 6h18"/><path d="M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>'],
    [/riwayat/i, '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>'],
    [/reset/i, '<circle cx="8" cy="15" r="4"/><path d="M10.8 12.2L20 3M17 6l3 3"/>'],
    [/tambah|buat baru/i, '<path d="M12 5v14M5 12h14"/>'],
    [/simpan|setujui|terima|konfirmasi|selesai/i, '<path d="M5 13l4 4L19 7"/>'],
    [/tolak|batal|tutup/i, '<path d="M18 6L6 18M6 6l12 12"/>'],
    [/cari|terapkan|filter/i, '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>'],
    [/unduh|download|pdf|csv/i, '<path d="M12 3v12M7 11l5 5 5-5"/><path d="M5 21h14"/>'],
    [/kembali/i, '<path d="M9 14L4 9l5-5"/><path d="M4 9h10a6 6 0 0 1 0 12h-3"/>'],
    [/lihat|detail|buka/i, '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/>']
  ];
  function addIcon(b, t) {
    if (b.tagName === 'INPUT' || b.querySelector('svg')) { return; }
    for (var i = 0; i < ICONS.length; i++) {
      if (ICONS[i][0].test(t)) {
        var s = document.createElementNS('http://www.w3.org/2000/svg', 'svg'); s.setAttribute('viewBox', '0 0 24 24'); s.innerHTML = ICONS[i][1];
        b.insertBefore(s, b.firstChild); return;
      }
    }
  }
  function isBoxy(cs) { return cs.boxShadow !== 'none' || px(cs.borderTopWidth) > 0 || cs.backgroundImage !== 'none' || !/rgba\(\s*0,\s*0,\s*0,\s*0\s*\)|transparent/.test(cs.backgroundColor); }
  function variant(t) {
    if (/hapus|tolak|batal|nonaktif|delete/i.test(t)) { return 'd'; }
    if (/edit|ubah|riwayat|detail|lihat|buka|unduh|reset|kembali$/i.test(t)) { return /unduh/i.test(t) ? 'g' : 's'; }
    return 'p';
  }

  function textEls(txt) {
    var w = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null), n, res = [], t0 = txt.toLowerCase();
    while ((n = w.nextNode())) {
      var p = n.parentElement;
      if (!p || /^(SCRIPT|STYLE|NOSCRIPT|OPTION)$/.test(p.tagName)) { continue; }
      if (n.nodeValue.replace(/\s+/g, ' ').trim().toLowerCase() === t0) { res.push(p); }
    }
    return res;
  }
  function findBar() {
    var hit = textEls('Pastel Dream')[0];
    if (!hit) { hit = textEls('Administrator')[0] || textEls('Siswa')[0]; }
    if (!hit) { return null; }
    var a = hit, best = null, vw = document.documentElement.clientWidth;
    while (a && a !== document.body) {
      var r = a.getBoundingClientRect();
      if (a.tagName === 'HEADER' && r.width >= vw * 0.4) { return a; }
      if (r.width >= vw * 0.5 && r.height >= 40 && r.height <= 130) { best = a; }
      a = a.parentElement;
    }
    return best;
  }
  function why(msg) { document.documentElement.setAttribute('data-tlu-why', msg); if (window.console) { console.info('[tl-ui] tidak aktif: ' + msg); } }

  ready(function () {
    if (window.__tluLoaded) { return; }
    window.__tluLoaded = true;
    var bar = findBar();
    if (!bar) { return why('topbar tidak ditemukan'); }
    var main = document.querySelector('main');
    if (!main || main.contains(bar) || bar.contains(main)) { main = bar.nextElementSibling; }
    if (!main) { return why('area konten tidak ditemukan'); }
    if (main.querySelector('.dz, .ax, .ad, .nb')) { return why('halaman punya desain sendiri (dashboard / form buku)'); }
    main.classList.add('tlu-main');
    document.documentElement.classList.add('tlu');
    document.documentElement.setAttribute('data-tlu', '1');

    /* kartu */
    safe(function () {
      [].forEach.call(main.querySelectorAll('div, section, form'), function (n) {
        if (n.closest('table') || n.classList.contains('tlu-hero')) { return; }
        var cs = getComputedStyle(n), w = n.getBoundingClientRect().width, h = n.getBoundingClientRect().height;
        if (px(cs.borderTopLeftRadius) >= 16 && w >= 220 && h >= 50 && isBoxy(cs)) { n.classList.add('tlu-card'); if (getComputedStyle(n).position === 'static') { n.style.position = 'relative'; } }
      });
    });

    /* judul halaman modern: breadcrumb + judul besar */
    safe(function () {
      bar.classList.add('tlu-bar');
      var t = bar.querySelector('.judul, h1, h2, h3, [class*="judul"], [class*="title"]');
      var title = t ? t.textContent.trim() : '';
      var titleEl = t;
      if (!title) {
        var w = document.createTreeWalker(bar, NodeFilter.SHOW_TEXT, null), n;
        while ((n = w.nextNode())) { var s = n.nodeValue.trim(); if (s.length >= 3 && s.length <= 40 && !/pastel|dream|administrator|siswa|admin trafie/i.test(s)) { title = s; titleEl = n.parentElement; break; } }
      }
      if (!title) { return; }
      var role = /administrator/i.test(bar.textContent) ? 'Admin' : 'Siswa';
      var own = main.querySelector('.xp-hd');
      if (own) { if (titleEl) { titleEl.classList.add('tlu-hide'); } return; }
      if (main.querySelector('.tlu-hero, h1')) { return; }
      var info = HERO[title.toLowerCase()];
      var h = el('header', 'tlu-hero');
      var box = el('div');
      var crumb = el('div', 'crumb'); crumb.innerHTML = '<span>' + role + '</span><svg viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg><b></b>'; crumb.querySelector('b').textContent = title;
      var h1 = el('h1'); h1.textContent = title;
      box.appendChild(crumb); box.appendChild(h1);
      if (info) { var p = el('p'); p.textContent = info[0]; box.appendChild(p); }
      h.appendChild(box);
      main.insertBefore(h, main.firstElementChild);
      if (titleEl) { titleEl.classList.add('tlu-hide'); }
    });

    /* tab bersegmen */
    safe(function () {
      var seen = [];
      [].forEach.call(main.querySelectorAll('a, button'), function (a) {
        var par = a.parentElement;
        if (!par || seen.indexOf(par) !== -1 || par.closest('table, nav, aside, header') ) { return; }
        var kids = [].slice.call(par.children);
        if (kids.length < 3 || kids.length > 9) { return; }
        var ok = kids.every(function (k) { return /^(A|BUTTON)$/.test(k.tagName) && k.textContent.trim().length <= 26; });
        if (!ok) { return; }
        seen.push(par);
        var act = kids.map(function (k) {
          var cs = getComputedStyle(k);
          return k.classList.contains('active') || k.classList.contains('on') || k.classList.contains('act') || !!k.getAttribute('aria-current') || cs.backgroundImage.indexOf('gradient') !== -1;
        });
        par.classList.add('tlu-seg');
        kids.forEach(function (k, i) { if (act[i]) { k.classList.add('on'); } });
      });
    });

    /* tombol */
    safe(function () {
      [].forEach.call(main.querySelectorAll('a, button, input[type="submit"]'), function (b) {
        if (b.closest('.tlu-seg, nav, aside, header.tlu-hero') || b.classList.contains('tlr-chip')) { return; }
        var cs = getComputedStyle(b), r = b.getBoundingClientRect(), t = (b.value || b.textContent || '').trim();
        if (!t || t.length > 28 || r.height > 64 || r.width > 280 || px(cs.borderTopLeftRadius) < 10) { return; }
        var grad = cs.backgroundImage.indexOf('gradient') !== -1, bg = !/rgba\(\s*0,\s*0,\s*0,\s*0\s*\)|transparent/.test(cs.backgroundColor);
        if (!grad && !bg && px(cs.borderTopWidth) === 0) { return; }
        b.classList.add('tlu-btn', variant(t));
        addIcon(b, t);
      });
      [].forEach.call(main.querySelectorAll('td'), function (td) {
        var bt = td.querySelectorAll('.tlu-btn');
        if (bt.length >= 2 && !td.querySelector('.tlu-acts')) {
          var w = el('div', 'tlu-acts'); [].slice.call(td.childNodes).forEach(function (c) { if (c.nodeType === 1 || (c.nodeType === 3 && c.nodeValue.trim())) { w.appendChild(c); } });
          td.appendChild(w);
        }
      });
    });

    /* lencana status */
    safe(function () {
      [].forEach.call(main.querySelectorAll('tbody td'), function (td) {
        var inner = td.children.length === 1 && !td.children[0].children.length ? td.children[0] : null;
        var tgt = inner || td;
        if (tgt.classList.contains('tlr-bd') || tgt.closest('.tlr-bd')) { return; }
        var txt = tgt.textContent.replace(/[\u2022\u25CF]/g, '').trim();
        if (!txt || txt.length > 24) { return; }
        for (var i = 0; i < BADGES.length; i++) {
          if (BADGES[i][0].test(txt)) {
            if (!inner) { var s = el('span'); s.textContent = txt; td.textContent = ''; td.appendChild(s); tgt = s; }
            else if (!tgt.children.length) { tgt.textContent = txt; }
            tgt.classList.add('tlr-bd', 'tlr-bd-' + BADGES[i][1]); return;
          }
        }
      });
    });

    /* avatar inisial pada kolom pertama (Siswa / Nama / Peminjam) */
    safe(function () {
      [].forEach.call(main.querySelectorAll('table'), function (tb) {
        var th0 = tb.querySelector('thead th'); if (!th0 || !/siswa|nama|peminjam|pengguna/i.test(th0.textContent)) { return; }
        [].forEach.call(tb.querySelectorAll('tbody tr'), function (tr) {
          var td = tr.children[0]; if (!td || td.hasAttribute('colspan') || td.querySelector('.tlu-av')) { return; }
          var nm = (td.querySelector('b, strong') || td).textContent.trim().split('\n')[0].trim(); if (!nm) { return; }
          var parts = nm.split(/\s+/), ini = (parts[0].charAt(0) + (parts.length > 1 ? parts[1].charAt(0) : '')).toUpperCase();
          var hue = 0; for (var i = 0; i < nm.length; i++) { hue = (hue * 31 + nm.charCodeAt(i)) % 360; }
          var av = el('span', 'tlu-av'); av.textContent = ini; av.style.background = 'linear-gradient(135deg, hsl(' + hue + ',70%,62%), hsl(' + ((hue + 40) % 360) + ',70%,55%))';
          var wrap = el('div', 'tlu-cell'), body = el('div');
          while (td.firstChild) { body.appendChild(td.firstChild); }
          wrap.appendChild(av); wrap.appendChild(body); td.appendChild(wrap);
        });
      });
    });

    /* menu sidebar */
    safe(function () {
      var side = document.querySelector('aside'); if (!side) { return; }
      [].forEach.call(side.querySelectorAll('a'), function (a) {
        if (!a.querySelector('svg') || a.textContent.trim().length > 32) { return; }
        var cs = getComputedStyle(a), on = a.classList.contains('active') || a.classList.contains('on') || !!a.getAttribute('aria-current') || cs.backgroundImage.indexOf('gradient') !== -1;
        a.classList.add('tlu-nav'); if (on) { a.classList.add('on'); }
      });
    });

    /* font modern */
    safe(function () {
      if (document.querySelector('link[href*="plus-jakarta-sans"]')) { return; }
      var l = el('link'); l.rel = 'stylesheet'; l.href = 'https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800'; document.head.appendChild(l);
    });
  });
})();