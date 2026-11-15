<style>
    @keyframes svPulse { 0%,100% { transform:scale(1); opacity:.55; } 50% { transform:scale(1.35); opacity:.15; } }
    @keyframes svTw { 0%,100% { opacity:0; transform:scale(.3) rotate(0deg); } 50% { opacity:1; transform:scale(1) rotate(45deg); } }
    @keyframes svRise { from { opacity:0; transform:translateY(18px) scale(.97); } to { opacity:1; transform:none; } }
    @keyframes svFlow { from { background-position:0 0; } to { background-position:200% 0; } }
    @keyframes svSpin { to { transform:rotate(360deg); } }

    .tl-stat.sv { position:relative; overflow:hidden; min-height:178px; padding:20px 22px 18px !important; display:flex; flex-direction:column;
        background:
            radial-gradient(110% 80% at 100% 0%, var(--gl) , transparent 62%),
            linear-gradient(160deg, rgba(255,255,255,.94), rgba(250,244,255,.7)) !important;
        box-shadow: 0 18px 44px -18px var(--sh), inset 0 1px 0 #fff, inset 0 0 0 1px rgba(255,255,255,.7) !important;
        animation: svRise .7s cubic-bezier(.2,.8,.2,1) backwards; }
    .tl-stat.sv:nth-child(2) { animation-delay:.08s; } .tl-stat.sv:nth-child(3) { animation-delay:.16s; } .tl-stat.sv:nth-child(4) { animation-delay:.24s; }
    .tl-stat.sv::before { display:none !important; }
    .tl-stat.sv > span:first-child { font-size:.7rem !important; letter-spacing:.14em; text-transform:uppercase; font-weight:600; color:#6b6790 !important; padding-right:52px; }
    .tl-stat.sv b.sv-num { font-size:2.7rem !important; line-height:1.05 !important; margin:8px 0 2px; font-family:'Playfair Display',Georgia,serif; font-weight:700;
        background:linear-gradient(120deg,var(--d1),var(--d2)) !important; -webkit-background-clip:text !important; background-clip:text !important; -webkit-text-fill-color:transparent !important; letter-spacing:-.01em; }
    .tl-stat.sv b.sv-num.sv-sm { font-size:2rem !important; }
    .tl-stat.sv > em { font-size:.7rem !important; color:#8b87a8 !important; }

    .sv-ico { position:absolute; right:18px; top:16px; width:44px; height:44px; border-radius:50%; display:grid; place-items:center; z-index:2;
        background:linear-gradient(145deg,var(--a1),var(--a2)); box-shadow:0 10px 22px -6px var(--sh), inset 0 2px 3px rgba(255,255,255,.8), inset 0 -3px 6px rgba(0,0,0,.08); }
    .sv-ico::before { content:''; position:absolute; inset:-7px; border-radius:50%; border:1.5px solid var(--a1); opacity:.4; animation:svPulse 3.2s ease-in-out infinite; }
    .sv-ico::after { content:''; position:absolute; left:7px; top:4px; width:18px; height:9px; border-radius:50%; background:linear-gradient(#fff,rgba(255,255,255,0)); opacity:.85; }
    .sv-ico svg { width:21px; height:21px; position:relative; z-index:1; filter:drop-shadow(0 1px 1px rgba(0,0,0,.18)); }
    .sv-star { position:absolute; width:11px; height:11px; z-index:1; pointer-events:none; clip-path:polygon(50% 0,61% 39%,100% 50%,61% 61%,50% 100%,39% 61%,0 50%,39% 39%); background:linear-gradient(135deg,#fff,var(--a1)); animation:svTw 4.2s ease-in-out infinite; }

    .sv-viz { margin-top:auto; padding-top:12px; position:relative; z-index:1; }
    .sv-track { position:relative; height:9px; border-radius:99px; background:rgba(120,100,190,.13); box-shadow:inset 0 1px 2px rgba(80,60,140,.15); display:flex; gap:3px; overflow:visible; }
    .sv-fill { height:100%; border-radius:99px; width:0; transition:width 1.4s cubic-bezier(.2,.8,.2,1); position:relative;
        background:linear-gradient(90deg,var(--c1),var(--c2),var(--c1)); background-size:200% 100%; animation:svFlow 4s linear infinite; box-shadow:0 3px 10px -2px var(--c1); }
    .sv-fill::after { content:''; position:absolute; left:3px; right:3px; top:1px; height:3px; border-radius:99px; background:rgba(255,255,255,.55); }
    .sv-fill.cap::before { content:''; position:absolute; right:-3px; top:50%; width:13px; height:13px; margin-top:-6.5px; border-radius:50%; background:#fff; box-shadow:0 0 0 3px var(--c1), 0 0 14px 2px var(--c1); z-index:2; }
    .sv-cap { display:flex; justify-content:space-between; margin-top:8px; font-size:.64rem; color:#7d79a0; font-weight:500; }
    .tl-stat.sv .sv-cap span { display:inline !important; font-size:.66rem !important; color:#7d79a0 !important; }
    .tl-stat.sv .sv-cap b { display:inline !important; font-size:.66rem !important; line-height:1.2 !important; font-family:inherit !important; margin:0 !important; background:none !important; -webkit-text-fill-color:#4b3f99 !important; color:#4b3f99 !important; font-weight:700; }

    .sv-panel { margin-top:1.25rem; position:relative; overflow:hidden; animation:svRise .8s .3s cubic-bezier(.2,.8,.2,1) backwards; }
    .sv-panel .sv-h { display:flex; align-items:center; gap:10px; margin-bottom:6px; }
    .sv-panel .sv-h h3 { margin:0; font-family:'Playfair Display',Georgia,serif; font-size:1.25rem; color:#3b2f86; }
    .sv-panel .sv-h i { width:14px; height:14px; clip-path:polygon(50% 0,61% 39%,100% 50%,61% 61%,50% 100%,39% 61%,0 50%,39% 39%); background:linear-gradient(135deg,#fff0fb,#c9a4ff,#ff9ed1); animation:svSpin 9s linear infinite; }
    .sv-panel .sv-sub { margin:0 0 14px; font-size:.78rem; color:#7d79a0; }
    .sv-grid { display:grid; grid-template-columns:1.25fr 1fr 1fr; gap:14px; }
    .sv-box { border-radius:30px 12px 30px 12px; padding:18px 20px; background:linear-gradient(160deg,rgba(255,255,255,.85),rgba(246,238,255,.55)); box-shadow:inset 0 1px 0 #fff, 0 12px 30px -18px rgba(120,90,200,.45); display:flex; align-items:center; gap:18px; min-width:0; }
    .sv-box:nth-child(even) { border-radius:12px 30px 12px 30px; }
    .sv-box h4 { margin:0 0 6px; font-size:.68rem; letter-spacing:.14em; text-transform:uppercase; color:#6b6790; font-weight:600; }
    .sv-ring { position:relative; width:132px; height:132px; flex:0 0 auto; }
    .sv-ring svg { width:100%; height:100%; transform:rotate(-90deg); overflow:visible; }
    .sv-ring circle { fill:none; stroke-width:15; }
    .sv-ring .trk { stroke:rgba(120,100,190,.13); }
    .sv-ring .seg { transition:stroke-dasharray 1.5s cubic-bezier(.2,.8,.2,1), stroke-dashoffset 1.5s cubic-bezier(.2,.8,.2,1); filter:drop-shadow(0 4px 6px rgba(120,80,200,.28)); }
    .sv-mid { position:absolute; inset:0; display:grid; place-content:center; text-align:center; }
    .sv-mid b { font-family:'Playfair Display',Georgia,serif; font-size:1.9rem; line-height:1; background:linear-gradient(120deg,#3b2f86,#c0408f); -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent; }
    .sv-mid small { font-size:.58rem; letter-spacing:.12em; text-transform:uppercase; color:#8b87a8; margin-top:4px; }
    .sv-leg { list-style:none; margin:0; padding:0; display:grid; gap:7px; font-size:.78rem; color:#4b476b; }
    .sv-leg li { display:flex; align-items:center; gap:8px; }
    .sv-leg li i { width:11px; height:11px; border-radius:50%; background:var(--c); box-shadow:0 0 0 3px rgba(255,255,255,.8), 0 0 10px var(--c); flex:0 0 auto; }
    .sv-leg li span { margin-left:auto; padding-left:10px; font-weight:600; color:#3b2f86; }
    .sv-note { font-size:.72rem; color:#7d79a0; margin:8px 0 0; line-height:1.5; }
    @media (max-width:1100px) { .sv-grid { grid-template-columns:1fr; } }
    @media (prefers-reduced-motion:reduce) { .tl-stat.sv, .sv-panel, .sv-ico::before, .sv-star, .sv-fill, .sv-panel .sv-h i { animation:none !important; } }
</style>
<script>
(function () {
    'use strict';
    var NS = 'http://www.w3.org/2000/svg', uid = 0;
    var ICONS = {
        total: '<path d="M12 3 3 8l9 5 9-5-9-5Z"/><path d="m3 13 9 5 9-5"/><path d="m3 17.5 9 5 9-5" opacity=".6"/>',
        borrow: '<path d="M2 5.5C4.5 4 8 4 12 6c4-2 7.5-2 10-.5V19c-2.5-1.5-6-1.5-10 .5-4-2-7.5-2-10-.5V5.5Z"/><path d="M12 6v13.5"/>',
        back: '<circle cx="12" cy="12" r="9"/><path d="m7.5 12.5 3 3 6-7"/>',
        fine: '<circle cx="12" cy="12" r="9"/><path d="M14.8 9.2c-.5-1-1.5-1.5-2.8-1.5-1.6 0-2.7.8-2.7 2 0 3 5.6 1.4 5.6 4.3 0 1.2-1.2 2-2.9 2-1.4 0-2.5-.6-3-1.7M12 6v1.7M12 16.3V18"/>',
        spark: '<path d="M12 2.5 14.2 9.8 21.5 12l-7.3 2.2L12 21.5l-2.2-7.3L2.5 12l7.3-2.2L12 2.5Z"/>'
    };
    var ACC = {
        total: ['#8b5cf6', '#c084fc', '#4c2fa8', '#a03a9a', 'rgba(167,139,250,.30)', 'rgba(139,92,246,.45)'],
        borrow: ['#38a8f8', '#7c8cff', '#1f5fb8', '#5b3fc4', 'rgba(125,190,255,.32)', 'rgba(56,168,248,.45)'],
        back: ['#22c597', '#5ddcc0', '#0f7a63', '#1c8f9e', 'rgba(110,231,190,.34)', 'rgba(34,197,151,.42)'],
        fine: ['#fb6f92', '#f7a1c4', '#b3315a', '#c0408f', 'rgba(255,160,190,.34)', 'rgba(251,111,146,.42)'],
        spark: ['#a78bfa', '#f0abfc', '#4c2fa8', '#a03a9a', 'rgba(200,170,255,.30)', 'rgba(167,139,250,.4)']
    };
    var ORDER = ['total', 'borrow', 'back', 'fine'];

    function kind(label, idx) {
        var l = (label || '').toLowerCase();
        if (l.indexOf('denda') > -1) return 'fine';
        if (l.indexOf('dikembali') > -1 || l.indexOf('selesai') > -1) return 'back';
        if (l.indexOf('pinjam') > -1 && l.indexOf('total') < 0) return 'borrow';
        if (l.indexOf('total') > -1) return 'total';
        return 'spark';
    }
    function num(s) { var d = String(s || '').replace(/[^\d]/g, ''); return d === '' ? null : parseInt(d, 10); }
    function fmt(n, rp) { var t = n.toLocaleString('id-ID'); return rp ? 'Rp ' + t : t; }
    function el(tag, cls, html) { var e = document.createElement(tag); if (cls) e.className = cls; if (html != null) e.innerHTML = html; return e; }
    function svgEl(tag, attrs) { var e = document.createElementNS(NS, tag); for (var k in attrs) { e.setAttribute(k, attrs[k]); } return e; }
    function pct(a, b) { return b > 0 ? Math.max(0, Math.min(100, Math.round(a / b * 100))) : 0; }

    function countUp(node, to, rp) {
        if (to == null) return;
        var t0 = null, dur = 1300;
        function step(ts) {
            if (t0 === null) t0 = ts;
            var p = Math.min(1, (ts - t0) / dur), e = 1 - Math.pow(1 - p, 4);
            node.textContent = fmt(Math.round(to * e), rp);
            if (p < 1) requestAnimationFrame(step);
        }
        node.textContent = fmt(0, rp);
        requestAnimationFrame(step);
    }

    function ringSvg(segs, total) {
        var R = 52, C = 2 * Math.PI * R, id = 'svg' + (++uid);
        var svg = svgEl('svg', { viewBox: '0 0 132 132' });
        var defs = svgEl('defs', {});
        segs.forEach(function (s, i) {
            var g = svgEl('linearGradient', { id: id + 'g' + i, x1: '0', y1: '0', x2: '1', y2: '1' });
            g.appendChild(svgEl('stop', { offset: '0', 'stop-color': s.c1 }));
            g.appendChild(svgEl('stop', { offset: '1', 'stop-color': s.c2 }));
            defs.appendChild(g);
        });
        svg.appendChild(defs);
        svg.appendChild(svgEl('circle', { cx: 66, cy: 66, r: R, 'class': 'trk' }));
        var live = segs.filter(function (s) { return s.v > 0; });
        var cum = 0, arcs = [];
        live.forEach(function (s) {
            var f = s.v / total, c = svgEl('circle', { cx: 66, cy: 66, r: R, 'class': 'seg', stroke: 'url(#' + id + 'g' + segs.indexOf(s) + ')' });
            var single = live.length === 1;
            c.setAttribute('stroke-linecap', single ? 'butt' : 'round');
            c.setAttribute('stroke-dasharray', '0 ' + C);
            c.setAttribute('stroke-dashoffset', String(-cum * C));
            var len = f * C - (single ? 0 : 15 + 5);
            arcs.push({ node: c, len: Math.max(0.01, len), off: -cum * C - (single ? 0 : 7.5), C: C });
            svg.appendChild(c);
            cum += f;
        });
        requestAnimationFrame(function () { requestAnimationFrame(function () {
            arcs.forEach(function (a) { a.node.setAttribute('stroke-dasharray', a.len + ' ' + a.C); a.node.setAttribute('stroke-dashoffset', String(a.off)); });
        }); });
        return svg;
    }

    function ring(segs, total, big, small) {
        var w = el('div', 'sv-ring');
        w.appendChild(ringSvg(segs, total > 0 ? total : 1));
        var m = el('div', 'sv-mid', '<b>' + big + '</b><small>' + small + '</small>');
        w.appendChild(m);
        return w;
    }

    function decorate(card, k, i) {
        var a = ACC[k] || ACC.spark;
        card.classList.add('sv');
        card.style.setProperty('--a1', a[0]); card.style.setProperty('--a2', a[1]);
        card.style.setProperty('--d1', a[2]); card.style.setProperty('--d2', a[3]);
        card.style.setProperty('--gl', a[4]); card.style.setProperty('--sh', a[5]);
        card.style.setProperty('--c1', a[0]); card.style.setProperty('--c2', a[1]);
        var ico = el('div', 'sv-ico', '<svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' + (ICONS[k] || ICONS.spark) + '</svg>');
        card.appendChild(ico);
        var s1 = el('i', 'sv-star'); s1.style.cssText = 'right:70px;top:14px;animation-delay:' + (i * .5) + 's';
        var s2 = el('i', 'sv-star'); s2.style.cssText = 'right:14px;top:66px;width:8px;height:8px;animation-delay:' + (1.4 + i * .4) + 's';
        card.appendChild(s1); card.appendChild(s2);
        var b = card.querySelector('b');
        if (b) {
            b.classList.add('sv-num');
            var txt = b.textContent.trim(), n = num(txt), rp = /^rp/i.test(txt);
            if (rp) b.classList.add('sv-sm');
            if (n != null && /^(rp\s*)?[\d.,\s]+$/i.test(txt)) { b.dataset.to = n; countUp(b, n, rp); }
        }
    }

    function meter(card, parts, capL, capR) {
        var v = el('div', 'sv-viz'), tr = el('div', 'sv-track');
        parts.forEach(function (p, i) {
            var f = el('div', 'sv-fill' + (i === parts.length - 1 && parts.length === 1 && p.w > 0 ? ' cap' : ''));
            f.style.setProperty('--c1', p.c1); f.style.setProperty('--c2', p.c2);
            f.dataset.w = p.w; tr.appendChild(f);
        });
        v.appendChild(tr);
        v.appendChild(el('div', 'sv-cap', '<span>' + capL + '</span><span>' + capR + '</span>'));
        card.appendChild(v);
        setTimeout(function () {
            [].forEach.call(tr.children, function (f) { f.style.width = (f.dataset.w > 0 ? 'calc(' + f.dataset.w + '% - 2px)' : '0'); });
        }, 250);
    }

    function build() {
        var cards = [].slice.call(document.querySelectorAll('.tl-stat'));
        cards = cards.filter(function (c) { return !c.dataset.sv && !c.closest('.ab, .tl-auth, [class*="lp-"]'); });
        if (!cards.length) return;
        var info = {};
        cards.forEach(function (c, i) {
            c.dataset.sv = '1';
            var sp = c.querySelector('span'), b = c.querySelector('b'), em = c.querySelector('em');
            var k = kind(sp ? sp.textContent : '', i);
            var rec = { card: c, k: k, v: b ? num(b.textContent) : null, em: em ? em.textContent : '' };
            if (!info[k]) info[k] = rec;
            c._sv = rec;
        });
        cards.forEach(function (c, i) { decorate(c, c._sv.k, i); });

        var T = info.total ? info.total.v : null, B = info.borrow ? info.borrow.v : null, R = info.back ? info.back.v : null, F = info.fine ? info.fine.v : null;
        var unpaid = null;
        if (info.fine) { var m = /rp\s*([\d.,]+)/i.exec(info.fine.em); if (m) unpaid = num(m[1]); }
        var haveT = T != null;

        if (haveT && info.total) {
            var oth = Math.max(0, T - (B || 0) - (R || 0));
            var parts = [];
            if (B) parts.push({ w: pct(B, T), c1: ACC.borrow[0], c2: ACC.borrow[1] });
            if (R) parts.push({ w: pct(R, T), c1: ACC.back[0], c2: ACC.back[1] });
            if (oth) parts.push({ w: pct(oth, T), c1: ACC.total[0], c2: ACC.total[1] });
            if (!parts.length) parts.push({ w: 0, c1: ACC.total[0], c2: ACC.total[1] });
            meter(info.total.card, parts, 'komposisi status', '<b>' + T + '</b> pengajuan');
        }
        if (haveT && info.borrow && B != null) meter(info.borrow.card, [{ w: pct(B, T), c1: ACC.borrow[0], c2: ACC.borrow[1] }], '<b>' + pct(B, T) + '%</b> dari total', B + ' / ' + T);
        if (haveT && info.back && R != null) meter(info.back.card, [{ w: pct(R, T), c1: ACC.back[0], c2: ACC.back[1] }], '<b>' + pct(R, T) + '%</b> dari total', R + ' / ' + T);
        if (info.fine && F != null) {
            var up = unpaid == null ? 0 : unpaid, paid = Math.max(0, F - up);
            meter(info.fine.card, [{ w: F > 0 ? pct(paid, F) : 0, c1: ACC.back[0], c2: ACC.back[1] }, { w: F > 0 ? pct(up, F) : 0, c1: ACC.fine[0], c2: ACC.fine[1] }],
                F > 0 ? '<b>' + pct(paid, F) + '%</b> terbayar' : 'tidak ada denda', F > 0 ? pct(up, F) + '% belum' : 'bersih');
        }
        cards.forEach(function (c) {
            if (!c.querySelector('.sv-viz')) {
                var v = el('div', 'sv-viz'); v.appendChild(el('div', 'sv-track', '<div class="sv-fill" style="width:62%;--c1:' + ACC.spark[0] + ';--c2:' + ACC.spark[1] + '"></div>'));
                c.appendChild(v);
            }
        });

        if (haveT && /\/admin\/reports/.test(location.pathname) && !document.querySelector('.sv-panel')) {
            var host = info.total.card.parentNode;
            var oth2 = Math.max(0, T - (B || 0) - (R || 0));
            var panel = el('div', 'tl-card sv-panel');
            panel.appendChild(el('div', 'sv-h', '<i></i><h3>Analisis Statistik</h3>'));
            panel.appendChild(el('p', 'sv-sub', T > 0 ? 'Gambaran lengkap dari ' + T + ' pengajuan sesuai filter yang kamu pilih.' : 'Belum ada pengajuan pada filter ini. Ubah rentang tanggal untuk melihat statistik.'));
            var grid = el('div', 'sv-grid');

            var b1 = el('div', 'sv-box');
            b1.appendChild(ring([
                { v: B || 0, c1: ACC.borrow[0], c2: ACC.borrow[1] },
                { v: R || 0, c1: ACC.back[0], c2: ACC.back[1] },
                { v: oth2, c1: ACC.total[0], c2: ACC.total[1] }
            ], T, T, 'pengajuan'));
            var d1 = el('div'); d1.style.minWidth = 0;
            d1.appendChild(el('h4', null, 'Komposisi status'));
            d1.appendChild(el('ul', 'sv-leg',
                '<li style="--c:' + ACC.borrow[0] + '"><i></i>Dipinjam<span>' + (B || 0) + ' &middot; ' + pct(B || 0, T) + '%</span></li>' +
                '<li style="--c:' + ACC.back[0] + '"><i></i>Dikembalikan<span>' + (R || 0) + ' &middot; ' + pct(R || 0, T) + '%</span></li>' +
                (oth2 ? '<li style="--c:' + ACC.total[0] + '"><i></i>Lainnya<span>' + oth2 + ' &middot; ' + pct(oth2, T) + '%</span></li>' : '')));
            b1.appendChild(d1); grid.appendChild(b1);

            var rate = pct(R || 0, T);
            var b2 = el('div', 'sv-box');
            b2.appendChild(ring([{ v: R || 0, c1: ACC.back[0], c2: ACC.back[1] }], T, rate + '%', 'kembali'));
            var d2 = el('div'); d2.style.minWidth = 0;
            d2.appendChild(el('h4', null, 'Tingkat pengembalian'));
            d2.appendChild(el('p', 'sv-note', T > 0 ? (R || 0) + ' dari ' + T + ' buku sudah dikembalikan, ' + (B || 0) + ' masih dipinjam.' : 'Menunggu data.'));
            b2.appendChild(d2); grid.appendChild(b2);

            var up2 = unpaid == null ? 0 : unpaid, F2 = F || 0, paid2 = Math.max(0, F2 - up2);
            var b3 = el('div', 'sv-box');
            b3.appendChild(ring([{ v: paid2, c1: ACC.back[0], c2: ACC.back[1] }, { v: up2, c1: ACC.fine[0], c2: ACC.fine[1] }], F2, F2 > 0 ? pct(paid2, F2) + '%' : '\u2728', F2 > 0 ? 'terbayar' : 'bersih'));
            var d3 = el('div'); d3.style.minWidth = 0;
            d3.appendChild(el('h4', null, 'Status denda'));
            d3.appendChild(el('p', 'sv-note', F2 > 0 ? 'Total ' + fmt(F2, true) + '. Belum dibayar ' + fmt(up2, true) + '.' : 'Tidak ada denda pada filter ini. Semua tertib!'));
            b3.appendChild(d3); grid.appendChild(b3);

            panel.appendChild(grid);
            host.insertAdjacentElement('afterend', panel);
        }
    }

    function go() { try { build(); } catch (e) { if (window.console) console.warn('statviz', e); } }
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', go); } else { go(); }
    window.addEventListener('load', function () { setTimeout(go, 120); });
})();
</script>