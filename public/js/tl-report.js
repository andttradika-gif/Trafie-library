/* TRAFIE-LIBRARY : Laporan Peminjaman - PDF + tampilan rapi. Mengenali elemen lewat struktur & tulisan. */
(function () {
  'use strict';
  function ready(fn) { if (document.readyState !== 'loading') fn(); else document.addEventListener('DOMContentLoaded', fn); }
  function safe(fn) { try { fn(); } catch (e) { if (window.console) console.warn('[tl-report]', e); } }
  function px(v) { var n = parseFloat(v); return isNaN(n) ? 0 : n; }
  function norm(s) { return (s || '').replace(/\s+/g, ' ').trim().toLowerCase(); }
  function el(tag, cls, text) { var e = document.createElement(tag); if (cls) { e.className = cls; } if (text !== undefined) { e.textContent = text; } return e; }
  function lines(node) { return (node.innerText || node.textContent || '').split('\n').map(function (s) { return s.trim(); }).filter(Boolean); }

  function findNodes(txt) {
    var w = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, null), n, t0 = norm(txt), res = [];
    while ((n = w.nextNode())) {
      var p = n.parentElement;
      if (!p || /^(SCRIPT|STYLE|NOSCRIPT|OPTION)$/.test(p.tagName)) { continue; }
      if (norm(n.nodeValue) === t0) { res.push(p); }
    }
    return res;
  }
  function cardOf(e, minW) {
    var a = e;
    for (var i = 0; i < 8 && a && a !== document.body; i++) {
      var cs = getComputedStyle(a), box = cs.boxShadow !== 'none' || px(cs.borderTopWidth) > 0 || cs.backgroundImage !== 'none' || !/rgba\(\s*0,\s*0,\s*0,\s*0\s*\)|transparent/.test(cs.backgroundColor);
      if (px(cs.borderTopLeftRadius) >= 14 && box && a.getBoundingClientRect().width >= (minW || 140)) { return a; }
      a = a.parentElement;
    }
    return null;
  }
  function loadScript(src) {
    return new Promise(function (ok, fail) {
      var s = document.createElement('script'); s.src = src; s.onload = ok; s.onerror = function () { fail(new Error('gagal memuat ' + src)); };
      document.head.appendChild(s);
    });
  }
  function toast(msg) {
    var t = el('div', 'tlr-toast', msg); document.body.appendChild(t);
    setTimeout(function () { t.classList.add('out'); setTimeout(function () { t.remove(); }, 400); }, 3200);
  }
  function ymd(d) { return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2); }

  ready(function () {
    if (window.__tlrLoaded) { return; }
    window.__tlrLoaded = true;
    var csvNodes = findNodes('Unduh CSV');
    var table = document.querySelector('main table') || document.querySelector('table');
    if (!csvNodes.length || !table) { return; }
    document.documentElement.classList.add('tlr');
    var form = table.closest('main') ? (table.closest('main').querySelector('form')) : document.querySelector('form');
    var dates = form ? form.querySelectorAll('input[type="date"]') : [];
    var statusSel = form ? form.querySelector('select') : null;

    /* ---------- tabel: pembungkus, lencana status, pencarian ---------- */
    safe(function () {
      table.classList.add('tlr-table');
      var wrap = el('div', 'tlr-scroll'); table.parentNode.insertBefore(wrap, table); wrap.appendChild(table);
      var map = [[/^dipinjam$/i, 'blue'], [/dikembalikan|selesai|lunas|disetujui/i, 'green'], [/menunggu|pending/i, 'amber'], [/ditolak|rejected/i, 'pink'], [/terlambat|belum dibayar/i, 'red']];
      [].forEach.call(table.querySelectorAll('tbody td'), function (td) {
        var inner = td.children.length === 1 && !td.children[0].children.length ? td.children[0] : null;
        var txt = (inner || td).textContent.trim();
        if (!txt || txt.length > 22) { return; }
        for (var i = 0; i < map.length; i++) {
          if (map[i][0].test(txt)) {
            var tgt = inner;
            if (!tgt) { tgt = el('span'); tgt.textContent = txt; td.textContent = ''; td.appendChild(tgt); }
            tgt.classList.add('tlr-bd', 'tlr-bd-' + map[i][1]); return;
          }
        }
      });
      var rows = [].slice.call(table.querySelectorAll('tbody tr'));
      var bar = el('div', 'tlr-bar');
      var inp = el('input'); inp.type = 'search'; inp.placeholder = 'Cari siswa, buku, atau status...';
      var cnt = el('span', 'count');
      bar.appendChild(inp); bar.appendChild(cnt); wrap.parentNode.insertBefore(bar, wrap);
      function upd() {
        var q = norm(inp.value), n = 0;
        rows.forEach(function (r) { var show = !q || norm(r.textContent).indexOf(q) !== -1; r.style.display = show ? '' : 'none'; if (show) { n++; } });
        cnt.textContent = n + ' data';
      }
      inp.addEventListener('input', upd); upd();
    });

    /* ---------- rentang cepat ---------- */
    safe(function () {
      if (!form || dates.length < 2) { return; }
      var box = el('div', 'tlr-chips'); box.appendChild(el('span', 'lb', 'Rentang cepat:'));
      var now = new Date();
      var set = [
        ['Hari ini', function () { return [now, now]; }],
        ['7 hari terakhir', function () { var d = new Date(now); d.setDate(d.getDate() - 6); return [d, now]; }],
        ['30 hari terakhir', function () { var d = new Date(now); d.setDate(d.getDate() - 29); return [d, now]; }],
        ['Bulan ini', function () { return [new Date(now.getFullYear(), now.getMonth(), 1), new Date(now.getFullYear(), now.getMonth() + 1, 0)]; }],
        ['Tahun ini', function () { return [new Date(now.getFullYear(), 0, 1), new Date(now.getFullYear(), 11, 31)]; }]
      ];
      set.forEach(function (s) {
        var b = el('button', 'tlr-chip', s[0]); b.type = 'button';
        b.addEventListener('click', function () { var r = s[1](); dates[0].value = ymd(r[0]); dates[1].value = ymd(r[1]); if (form.requestSubmit) { form.requestSubmit(); } else { form.submit(); } });
        box.appendChild(b);
      });
      form.appendChild(box);
    });

    /* ---------- kartu ringkasan ---------- */
    var stats = [];
    safe(function () {
      ['Total Pengajuan', 'Dipinjam', 'Dikembalikan', 'Total Denda'].forEach(function (lab, i) {
        var n = findNodes(lab).filter(function (x) { return !x.closest('table, form, select'); })[0];
        if (!n) { return; }
        var c = cardOf(n, 140); if (!c) { return; }
        c.classList.add('tlr-stat', 'tlr-s' + (i + 1)); stats.push(c);
      });
    });

    /* ---------- tombol PDF ---------- */
    safe(function () {
      var node = csvNodes[0];
      var btn = node.closest('a, button') || node;
      btn.classList.add('tlr-pdf');
      btn.removeAttribute('download'); if (btn.tagName === 'A') { btn.setAttribute('href', '#'); }
      btn.onclick = null;
      node.textContent = 'Unduh PDF';
      var ic = document.createElementNS('http://www.w3.org/2000/svg', 'svg'); ic.setAttribute('viewBox', '0 0 24 24');
      ic.innerHTML = '<path d="M12 3v12M7 11l5 5 5-5"/><path d="M5 21h14"/>';
      node.parentNode.insertBefore(ic, node);
      btn.addEventListener('click', function (e) {
        e.preventDefault(); e.stopImmediatePropagation();
        if (btn.classList.contains('busy')) { return; }
        btn.classList.add('busy'); node.textContent = 'Menyiapkan PDF...';
        makePdf().then(function () { toast('PDF berhasil dibuat.'); })
          .catch(function () { toast('Pustaka PDF tidak bisa dimuat. Gunakan jendela cetak: pilih "Simpan sebagai PDF".'); setTimeout(function () { window.print(); }, 600); })
          .then(function () { btn.classList.remove('busy'); node.textContent = 'Unduh PDF'; });
      }, true);
    });

    function makePdf() {
      var libs = window.jspdf ? Promise.resolve() : loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js');
      return libs.then(function () {
        return (window.jspdf && window.jspdf.jsPDF && (new window.jspdf.jsPDF()).autoTable) ? null : loadScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js');
      }).then(function () {
        var doc = new window.jspdf.jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });
        var W = doc.internal.pageSize.getWidth();
        var head = [], body = [];
        [].forEach.call(table.querySelectorAll('thead th'), function (th) { head.push(th.textContent.trim()); });
        [].forEach.call(table.querySelectorAll('tbody tr'), function (tr) {
          if (tr.style.display === 'none') { return; }
          var cells = [].map.call(tr.children, function (td) { return lines(td).join('\n') || '-'; });
          if (cells.length === head.length) { body.push(cells); }
        });
        var stamp = new Date().toLocaleString('id-ID');
        doc.setFillColor(139, 111, 224); doc.rect(0, 0, W, 70, 'F');
        doc.setTextColor(255, 255, 255); doc.setFont('helvetica', 'bold'); doc.setFontSize(20); doc.text('Trafie-library', 40, 33);
        doc.setFont('helvetica', 'normal'); doc.setFontSize(9); doc.text('Where Every Book Blooms Into a Universe', 40, 50);
        doc.setFont('helvetica', 'bold'); doc.setFontSize(15); doc.text('Laporan Peminjaman', W - 40, 33, { align: 'right' });
        doc.setFont('helvetica', 'normal'); doc.setFontSize(9); doc.text('Dicetak: ' + stamp, W - 40, 50, { align: 'right' });

        doc.setTextColor(70, 64, 110); doc.setFontSize(10);
        var per = dates.length >= 2 ? (dates[0].value || '-') + '  s/d  ' + (dates[1].value || '-') : '-';
        var st = statusSel ? statusSel.options[statusSel.selectedIndex].text : 'Semua';
        doc.text('Periode: ' + per + '      Status: ' + st, 40, 92);

        var x = 40, y = 106, bw = (W - 80 - 3 * 12) / 4;
        var colors = [[231, 224, 252], [220, 232, 252], [219, 243, 230], [253, 228, 238]];
        stats.forEach(function (c, i) {
          var l = lines(c); doc.setFillColor.apply(doc, colors[i % 4]); doc.roundedRect(x + i * (bw + 12), y, bw, 50, 8, 8, 'F');
          doc.setTextColor(110, 104, 150); doc.setFontSize(8); doc.text(l[0] || '', x + i * (bw + 12) + 12, y + 16);
          doc.setTextColor(45, 42, 74); doc.setFont('helvetica', 'bold'); doc.setFontSize(15); doc.text(l[1] || '', x + i * (bw + 12) + 12, y + 37); doc.setFont('helvetica', 'normal');
        });

        if (!body.length) { body.push(['Tidak ada data pada filter ini.'].concat(new Array(Math.max(0, head.length - 1)).fill(''))); }
        doc.autoTable({
          head: [head], body: body, startY: stats.length ? y + 66 : 108, theme: 'grid',
          styles: { fontSize: 9, cellPadding: 6, lineColor: [226, 220, 246], lineWidth: 0.5, textColor: [58, 53, 96], valign: 'middle' },
          headStyles: { fillColor: [139, 111, 224], textColor: 255, fontStyle: 'bold' },
          alternateRowStyles: { fillColor: [248, 244, 255] },
          margin: { left: 40, right: 40, bottom: 40 },
          didDrawPage: function () {
            var n = doc.internal.getNumberOfPages(), h = doc.internal.pageSize.getHeight();
            doc.setFontSize(8); doc.setTextColor(140, 134, 175);
            doc.text('Trafie-library  |  Halaman ' + doc.internal.getCurrentPageInfo().pageNumber, W - 40, h - 20, { align: 'right' });
            doc.text('Dokumen ini dibuat otomatis oleh sistem perpustakaan.', 40, h - 20);
          }
        });
        doc.save('laporan-peminjaman-' + ymd(new Date()) + '.pdf');
      });
    }
  });
})();