<style>
    @keyframes skDrift { 0%,100% { transform:translate(0,0) scale(1); } 50% { transform:translate(60px,44px) scale(1.15); } }
    @keyframes skBob { 0%,100% { transform:translateY(0) rotate(var(--r,0deg)); } 50% { transform:translateY(-34px) rotate(calc(var(--r,0deg) + 12deg)); } }
    @keyframes skTw { 0%,100% { opacity:0; transform:scale(.3) rotate(0deg); } 50% { opacity:1; transform:scale(1) rotate(45deg); } }
    @keyframes skRay { 0%,100% { opacity:.2; transform:rotate(24deg) translateX(0); } 50% { opacity:.55; transform:rotate(24deg) translateX(70px); } }
    @keyframes skSweep { 0%,55% { left:-70%; } 100% { left:150%; } }

    /* ---------- latar mimpi (halaman non-katalog) ---------- */
    .sk-host { position:relative; isolation:isolate; }
    .sk-dream { position:absolute; left:0; right:0; top:0; z-index:-1; overflow:hidden; pointer-events:none; }
    .sk-dream > i { position:absolute; display:block; }
    .sk-orb { border-radius:50%; filter:blur(70px); opacity:.5; animation:skDrift ease-in-out infinite; }
    .sk-ray { width:190px; height:130%; top:-15%; background:linear-gradient(to bottom,rgba(255,255,255,0),rgba(255,255,255,.7) 40%,rgba(255,214,240,.3) 70%,rgba(255,255,255,0)); filter:blur(26px); transform:rotate(24deg); animation:skRay 13s ease-in-out infinite; }
    .sk-dia { clip-path:polygon(25% 0,75% 0,100% 35%,50% 100%,0 35%); background:linear-gradient(135deg,rgba(255,255,255,.95),rgba(215,200,255,.5) 40%,rgba(255,190,225,.55) 75%,rgba(255,255,255,.8)); animation:skBob ease-in-out infinite; }
    .sk-dia::after { content:''; position:absolute; inset:0; background:linear-gradient(to bottom,transparent 34%,rgba(255,255,255,.9) 35%,transparent 37%), linear-gradient(62deg,transparent 48.5%,rgba(255,255,255,.75) 50%,transparent 51.5%), linear-gradient(-62deg,transparent 48.5%,rgba(255,255,255,.75) 50%,transparent 51.5%); }
    .sk-st { clip-path:polygon(50% 0,61% 39%,100% 50%,61% 61%,50% 100%,39% 61%,0 50%,39% 39%); background:linear-gradient(135deg,#fff,#ffd3ea 45%,#c9b6ff); opacity:0; animation:skTw ease-in-out infinite; }
    .sk-gl { border-radius:50%; background:#fff; box-shadow:0 0 8px 2px rgba(255,255,255,.95), 0 0 16px rgba(240,111,176,.55); opacity:0; animation:skTw ease-in-out infinite; }
    .sk-bub { border-radius:50%; background:radial-gradient(circle at 30% 28%,rgba(255,255,255,.95),rgba(255,255,255,.25) 38%,rgba(190,170,255,.28) 72%,rgba(255,190,225,.32)); border:1px solid rgba(255,255,255,.8); box-shadow:inset 0 -8px 18px rgba(255,255,255,.45), 0 12px 32px rgba(124,92,224,.2); animation:skBob ease-in-out infinite; }

    /* ---------- kartu kaca organik ---------- */
    .tl-card { position:relative; border-radius:42px 16px 42px 16px; background:linear-gradient(150deg,rgba(255,255,255,.9),rgba(255,255,255,.5)); border:1px solid rgba(255,255,255,.95); box-shadow:0 22px 48px rgba(124,92,224,.14), inset 0 2px 0 #fff, inset 0 -14px 30px rgba(190,170,255,.1); }
    .tl-grid > .tl-card:nth-child(even) { border-radius:16px 42px 16px 42px; }
    .tl-hero { border-radius:54px 18px 54px 18px; box-shadow:0 26px 60px rgba(124,92,224,.2), inset 0 2px 0 #fff; }

    /* kartu statistik: kilau + bintang + angka gradasi */
    .tl-stat { overflow:hidden; }
    .tl-stat::before { content:''; position:absolute; right:22px; top:20px; width:16px; height:16px; clip-path:polygon(50% 0,61% 39%,100% 50%,61% 61%,50% 100%,39% 61%,0 50%,39% 39%); background:linear-gradient(135deg,#fff,#d9c8ff); animation:skTw 4s ease-in-out infinite; }
    .tl-stat::after { content:''; position:absolute; top:-30%; left:-70%; width:38%; height:160%; background:linear-gradient(100deg,transparent,rgba(255,255,255,.6),transparent); transform:rotate(18deg); animation:skSweep 8s ease-in-out infinite; pointer-events:none; }
    .tl-stat b { background:linear-gradient(120deg,#3b2f86,#c0408f); -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent; }
    .tl-stat > * { position:relative; z-index:1; }

    /* ---------- tabel: baris melayang ---------- */
    .tl-table { border-collapse:separate; border-spacing:0 10px; }
    .tl-table th { border-bottom:0; padding:.75rem .9rem; background:#ede4fd; color:#5b46b3; }
    .tl-table th:first-child { border-radius:18px 0 0 18px; } .tl-table th:last-child { border-radius:0 18px 18px 0; }
    .tl-table td { border-bottom:0; background:rgba(255,255,255,.88); transition:background .2s, box-shadow .2s; }
    .tl-table td:first-child { border-radius:20px 0 0 20px; } .tl-table td:last-child { border-radius:0 20px 20px 0; }
    .tl-table tbody tr:hover td { background:#fff; box-shadow:0 12px 24px rgba(124,92,224,.16); }
    .tl-table td.tl-empty { border-radius:20px; }

    /* ---------- isian + tombol ---------- */
    .tl-input { border:1.5px solid transparent; border-radius:18px; background:rgba(124,92,224,.075); padding:.8rem 1rem; box-shadow:none; transition:background .18s, border-color .18s, box-shadow .18s; }
    .tl-input:hover { background:rgba(124,92,224,.11); }
    .tl-input:focus { background:#fff; border-color:#7c5ce0; box-shadow:0 0 0 4px rgba(124,92,224,.16), 0 12px 28px rgba(124,92,224,.22); }
    select.tl-input { appearance:none; -webkit-appearance:none; cursor:pointer; padding-right:2.4rem; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%237c5ce0' stroke-width='2.4' stroke-linecap='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 14px center; }
    .tl-btn { position:relative; overflow:hidden; background:linear-gradient(120deg,#7c5ce0,#c060d8 55%,#f06fb0); box-shadow:0 12px 26px rgba(124,92,224,.36), inset 0 1px 0 rgba(255,255,255,.4); }
    .tl-btn::after { content:''; position:absolute; top:0; left:-70%; width:40%; height:100%; background:linear-gradient(100deg,transparent,rgba(255,255,255,.6),transparent); transform:skewX(-20deg); transition:left .7s ease; pointer-events:none; }
    .tl-btn:hover { box-shadow:0 18px 34px rgba(124,92,224,.46), inset 0 1px 0 rgba(255,255,255,.4); }
    .tl-btn:hover::after { left:140%; }
    .tl-btn-ok, .tl-btn-danger { border-radius:999px; padding:.45rem 1rem; box-shadow:inset 0 1px 0 rgba(255,255,255,.75), 0 4px 10px rgba(124,92,224,.1); transition:transform .18s, box-shadow .18s; }
    .tl-btn-ok:hover, .tl-btn-danger:hover { transform:translateY(-2px); box-shadow:inset 0 1px 0 rgba(255,255,255,.75), 0 10px 20px rgba(124,92,224,.2); }

    /* ---------- status, lencana, notifikasi ---------- */
    .tl-st { box-shadow:inset 0 1px 0 rgba(255,255,255,.85), 0 4px 10px rgba(124,92,224,.12); }
    .tl-st::before { content:''; display:inline-block; width:6px; height:6px; margin-right:.4rem; border-radius:50%; background:currentColor; vertical-align:middle; opacity:.8; }
    .tl-badge { box-shadow:inset 0 1px 0 rgba(255,255,255,.85), 0 4px 10px rgba(124,92,224,.12); }
    .tl-alert { border-radius:24px 8px 24px 8px; box-shadow:0 10px 24px rgba(124,92,224,.1); }

    @media (max-width:640px) { .tl-card { border-radius:30px 12px 30px 12px; } .tl-grid > .tl-card:nth-child(even) { border-radius:12px 30px 12px 30px; } }
    @media (prefers-reduced-motion:reduce) { .sk-dream { display:none; } .tl-stat::before, .tl-stat::after { animation:none; } }
</style>
<script>
    (function () {
        if (window.__tlSkin) { return; }
        window.__tlSkin = true;
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }

        function build() {
            if (document.querySelector('.ab, .sk-dream')) { return; }
            if (document.querySelector('[class^="lp-"], [class*=" lp-"], .tl-auth')) { return; }
            var card = document.querySelector('.tl-card, .tl-hero');
            if (!card) { return; }
            var host = card.closest('main') || card.parentElement;
            if (!host || host === document.body) { return; }
            host.classList.add('sk-host');

            var H = Math.max(host.scrollHeight, 900), r = Math.random;
            var box = document.createElement('div');
            box.className = 'sk-dream'; box.setAttribute('aria-hidden', 'true'); box.style.height = H + 'px';
            function add(cls, css) { var e = document.createElement('i'); e.className = cls; e.style.cssText = css; box.appendChild(e); }
            function pct(a, b) { return (a + r() * (b - a)).toFixed(1) + '%'; }
            var cols = ['rgba(240,111,176,.8)', 'rgba(124,92,224,.75)', 'rgba(255,196,122,.7)', 'rgba(47,199,157,.5)', 'rgba(120,170,255,.7)'];
            var i, s, nO = Math.min(7, Math.ceil(H / 520));
            for (i = 0; i < nO; i++) {
                s = 360 + r() * 240;
                add('sk-orb', 'width:' + s + 'px;height:' + s + 'px;left:' + pct(-6, 80) + ';top:' + (i * (100 / nO) + r() * 5).toFixed(1) + '%;background:radial-gradient(closest-side,' + cols[i % 5] + ',transparent);animation-duration:' + (14 + r() * 10) + 's;animation-delay:-' + (r() * 10) + 's');
            }
            add('sk-ray', 'left:22%'); add('sk-ray', 'left:68%;animation-delay:-6s');
            var nD = Math.min(18, Math.ceil(H / 260) + 3);
            for (i = 0; i < nD; i++) {
                s = 24 + r() * 74;
                add('sk-dia', 'width:' + s + 'px;height:' + s + 'px;left:' + pct(-1, 95) + ';top:' + pct(0, 94) + ';opacity:' + (0.3 + r() * 0.35).toFixed(2) + ';--r:' + (r() * 40 - 20).toFixed(0) + 'deg;animation-duration:' + (9 + r() * 8) + 's;animation-delay:-' + (r() * 8) + 's');
            }
            var nS = Math.min(36, Math.ceil(H / 40));
            for (i = 0; i < nS; i++) {
                s = 8 + r() * 16;
                add('sk-st', 'width:' + s + 'px;height:' + s + 'px;left:' + pct(0, 98) + ';top:' + pct(0, 98) + ';animation-duration:' + (3 + r() * 4) + 's;animation-delay:' + (r() * 5) + 's');
            }
            var nG = Math.min(70, Math.ceil(H / 16));
            for (i = 0; i < nG; i++) {
                s = 2 + r() * 3;
                add('sk-gl', 'width:' + s + 'px;height:' + s + 'px;left:' + pct(0, 99) + ';top:' + pct(0, 99) + ';animation-duration:' + (2.4 + r() * 4) + 's;animation-delay:' + (r() * 6) + 's');
            }
            var nB = Math.min(9, Math.ceil(H / 190));
            for (i = 0; i < nB; i++) {
                s = 22 + r() * 54;
                add('sk-bub', 'width:' + s + 'px;height:' + s + 'px;left:' + pct(0, 96) + ';top:' + pct(0, 96) + ';opacity:' + (0.5 + r() * 0.35).toFixed(2) + ';--r:0deg;animation-duration:' + (11 + r() * 9) + 's;animation-delay:-' + (r() * 10) + 's');
            }
            host.insertBefore(box, host.firstChild);
        }

        if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', build); } else { build(); }
        window.addEventListener('load', function () { setTimeout(build, 80); });
    })();
</script>

<!-- dxp-fix -->
<style>
    /* pemulihan: banner dan kartu Reading XP di dashboard siswa */
    .d-hero { position:relative !important; overflow:hidden !important; min-height:120px !important; border:1px solid #fff !important;
        background: radial-gradient(420px 160px at 82% 100%, rgba(247,200,220,.65), transparent 70%), linear-gradient(100deg, rgba(255,255,255,.92) 0%, rgba(236,228,252,.92) 38%, rgba(207,232,247,.92) 100%) !important;
        box-shadow:0 10px 30px rgba(139,92,246,.08) !important; }
    .d-hero .castle { display:block !important; position:absolute !important; right:7% !important; bottom:-14px !important; height:158px !important; width:auto !important; opacity:1 !important; visibility:visible !important; pointer-events:none; z-index:1; }
    .d-xp { position:relative !important; overflow:hidden !important; color:#fff !important; border:1px solid rgba(255,255,255,.6) !important;
        background: radial-gradient(260px 160px at 12% 20%, rgba(255,255,255,.28), transparent 70%), radial-gradient(300px 200px at 90% 100%, rgba(247,160,208,.45), transparent 70%), radial-gradient(240px 160px at 55% 0%, rgba(150,200,255,.35), transparent 70%), linear-gradient(120deg,#5a49b8,#7b62d6 48%,#a68be8) !important;
        box-shadow:0 16px 36px rgba(110,80,200,.28) !important; }
    .d-xp .d-xpmid, .d-xp .d-xpmid .t, .d-xp .d-xpmid .n, .d-xp .d-xpmid .n small, .d-xp .d-xpmid .cap { color:#fff !important; -webkit-text-fill-color:#fff !important; }
    .d-xp .d-bar { background:rgba(255,255,255,.35) !important; }
    .d-xp .d-bar i { background:linear-gradient(90deg,#ffe3f1,#fff,#d9ccf5) !important; min-width:6px; }
</style>

<style>
/* sx-fix3-skin: kilap, cahaya dan kilau untuk banner, kartu level dan kartu statistik */
@keyframes sxGlow { 0%,100% { filter: drop-shadow(0 6px 14px rgba(167,139,250,.55)) drop-shadow(0 0 10px rgba(196,170,255,.55)); } 50% { filter: drop-shadow(0 8px 20px rgba(244,143,177,.55)) drop-shadow(0 0 20px rgba(244,143,177,.5)); } }
@keyframes sxGlint { 0% { background-position: 170% 0, 0 0; } 60%,100% { background-position: -80% 0, 0 0; } }
@keyframes sxTwk { 0%,100% { opacity: .25; transform: scale(.6) rotate(0deg); } 50% { opacity: 1; transform: scale(1) rotate(45deg); } }

html body .sx-level.sx-level, html body .sx-hero.sx-hero, html body .sx-stat.sx-stat { animation: sxGlow 5s ease-in-out infinite !important; position: relative; }
html body .sx-stat.sx-stat:nth-child(3n) { animation-delay: -1.6s !important; } html body .sx-stat.sx-stat:nth-child(2n) { animation-delay: -3.1s !important; }
html body .sx-level.sx-level, html body .sx-hero.sx-hero { box-shadow: inset 0 0 0 1.5px rgba(255,255,255,.95), inset 0 2px 0 #fff, inset 0 -18px 34px rgba(255,255,255,.16) !important; }
html body .sx-stat.sx-stat { box-shadow: inset 0 0 0 1.5px rgba(255,255,255,.95), inset 0 2px 0 #fff, inset 0 -16px 28px rgba(255,255,255,.35) !important; }

/* kilap kaca: pita cahaya bergerak + sorot atas */
html body .sx-level.sx-level::after, html body .sx-hero.sx-hero::before, html body .sx-stat.sx-stat::before {
    content: '' !important; position: absolute !important; inset: 0 !important; z-index: 0 !important; pointer-events: none !important; border-radius: inherit !important; opacity: 1 !important; mix-blend-mode: normal !important; transform: none !important; width: auto !important; height: auto !important; left: 0 !important; top: 0 !important; right: 0 !important; bottom: 0 !important; clip-path: none !important;
    background-color: transparent !important; background-repeat: no-repeat !important; background-size: 260% 100%, 100% 100% !important;
    background-image: linear-gradient(105deg, rgba(255,255,255,0) 42%, rgba(255,190,232,.75) 46%, rgba(255,255,255,1) 50%, rgba(170,215,255,.75) 54%, rgba(255,255,255,0) 58%), linear-gradient(180deg, rgba(255,255,255,.6) 0%, rgba(255,255,255,.12) 28%, rgba(255,255,255,0) 48%) !important;
    animation: sxGlint 6s ease-in-out infinite !important; }
html body .sx-hero.sx-hero::before { animation-delay: -2s !important; } html body .sx-stat.sx-stat::before { animation-duration: 8s !important; background-image: linear-gradient(105deg, rgba(255,255,255,0) 42%, rgba(255,255,255,.7) 50%, rgba(255,255,255,0) 58%), linear-gradient(180deg, rgba(255,255,255,.5) 0%, rgba(255,255,255,0) 40%) !important; }
html body .sx-stat.sx-stat:nth-child(3n)::before { animation-delay: -3s !important; } html body .sx-stat.sx-stat:nth-child(2n)::before { animation-delay: -5s !important; }
html body .sx-level.sx-level > *, html body .sx-stat.sx-stat > * { position: relative; z-index: 2; } html body .sx-stat.sx-stat .art { position: absolute !important; z-index: 1; }
html body .sx-hero.sx-hero .tx { position: relative; z-index: 3; }

/* banner: warna mimpi + kilau bintang (tanpa gambar) */
html body .sx-hero.sx-hero { background-image:
    radial-gradient(2px 2px at 52% 24%, #fff, transparent), radial-gradient(1.6px 1.6px at 61% 68%, #fff, transparent), radial-gradient(2.2px 2.2px at 73% 30%, #fff, transparent), radial-gradient(1.5px 1.5px at 84% 62%, #fff, transparent), radial-gradient(2px 2px at 92% 22%, #fff, transparent),
    radial-gradient(360px 170px at 86% 10%, rgba(255,190,225,.7), transparent 70%), radial-gradient(380px 190px at 66% 110%, rgba(170,210,255,.7), transparent 70%), radial-gradient(260px 140px at 98% 100%, rgba(200,170,255,.6), transparent 70%),
    linear-gradient(100deg,#fff 0%,#f4ecfe 40%,#e6ecfd 100%) !important; }
html body .sx-hero.sx-hero .tx::after { content: ''; position: absolute; left: calc(100% + 150px); top: 8px; width: 16px; height: 16px; clip-path: polygon(50% 0,61% 39%,100% 50%,61% 61%,50% 100%,39% 61%,0 50%,39% 39%); background: linear-gradient(135deg,#fff,#ffb8dc 60%,#c4a4ff); animation: sxTwk 3.2s ease-in-out infinite; pointer-events: none; }
html body .sx-hero.sx-hero h1 { text-shadow: 0 0 18px rgba(255,255,255,.95), 0 0 26px rgba(167,139,250,.45) !important; }
@media (prefers-reduced-motion: reduce) { html body .sx-level.sx-level, html body .sx-hero.sx-hero, html body .sx-stat.sx-stat, html body .sx-level.sx-level::after, html body .sx-hero.sx-hero::before, html body .sx-stat.sx-stat::before { animation: none !important; } }
</style>

<style>
/* sx-ring: cincin kaca berkilau yang berputar menyusuri bingkai */
@property --sxa { syntax: '<angle>'; inherits: false; initial-value: 0deg; }
@keyframes sxSpin { to { --sxa: 360deg; } }
html body .sx-level.sx-level::before, html body .sx-hero.sx-hero::after, html body .sx-stat.sx-stat::after {
    content: '' !important; position: absolute !important; left: 0 !important; top: 0 !important; right: 0 !important; bottom: 0 !important; width: auto !important; height: auto !important;
    inset: 0 !important; z-index: 4 !important; pointer-events: none !important; border-radius: inherit !important; padding: 3.5px !important; opacity: 1 !important; transform: none !important; clip-path: none !important; display: block !important;
    background: conic-gradient(from var(--sxa), rgba(255,255,255,0) 0deg, rgba(255,255,255,0) 190deg, rgba(190,215,255,.85) 255deg, #fff 290deg, rgba(255,170,225,.95) 318deg, rgba(255,255,255,0) 350deg) !important;
    -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0) !important; -webkit-mask-composite: xor !important;
    mask: linear-gradient(#000 0 0) content-box exclude, linear-gradient(#000 0 0) !important; mask-composite: exclude !important;
    -webkit-mask-image: linear-gradient(#000 0 0), linear-gradient(#000 0 0) !important; -webkit-mask-clip: content-box, border-box !important;
    filter: drop-shadow(0 0 5px rgba(255,255,255,.9)) !important;
    animation: sxSpin 5.5s linear infinite !important; mask-image: none; }
html body .sx-hero.sx-hero::after { animation-duration: 8s !important; -webkit-mask-image: linear-gradient(#000 0 0), linear-gradient(#000 0 0) !important; }
html body .sx-stat.sx-stat::after { animation-duration: 7s !important; padding: 3px !important; }
html body .sx-stat.sx-stat:nth-child(2n)::after { animation-delay: -2.4s !important; } html body .sx-stat.sx-stat:nth-child(3n)::after { animation-delay: -4.6s !important; }
@media (prefers-reduced-motion: reduce) { html body .sx-level.sx-level::before, html body .sx-hero.sx-hero::after, html body .sx-stat.sx-stat::after { animation: none !important; } }
</style>

<style>
/* sx-glass: efek kaca DI DALAM bingkai (cincin luar dimatikan) */
@keyframes sxBlob { from { background-position: 0% 50%, 100% 15%, 35% 100%; } to { background-position: 100% 35%, 0% 85%, 75% 0%; } }
@keyframes sxStreak { 0% { background-position: 160% 0; } 55%,100% { background-position: -70% 0; } }

/* matikan cincin luar */
html body .sx-stat.sx-stat::after { display: none !important; }

/* tepi bevel kaca di dalam bingkai */
html body .sx-level.sx-level, html body .sx-hero.sx-hero, html body .sx-stat.sx-stat {
    box-shadow: inset 0 0 0 1.5px rgba(255,255,255,.95), inset 0 3px 2px -1px #fff, inset 0 16px 26px -12px rgba(255,255,255,.85), inset 0 -18px 28px -14px rgba(255,255,255,.55), inset 14px 0 22px -16px rgba(255,255,255,.7), inset -14px 0 22px -16px rgba(255,255,255,.5) !important; }

/* pantulan cahaya melayang di dalam kaca */
html body .sx-level.sx-level::before, html body .sx-hero.sx-hero::after, html body .sx-stat.sx-stat::after {
    content: '' !important; display: block !important; position: absolute !important; left: 0 !important; top: 0 !important; right: 0 !important; bottom: 0 !important; inset: 0 !important; width: auto !important; height: auto !important;
    z-index: 0 !important; pointer-events: none !important; border-radius: inherit !important; padding: 0 !important; opacity: 1 !important; transform: none !important; clip-path: none !important; filter: none !important;
    -webkit-mask: none !important; mask: none !important; -webkit-mask-image: none !important; mask-image: none !important;
    background-color: transparent !important; background-repeat: no-repeat !important; background-size: 58% 130%, 46% 110%, 52% 120% !important;
    background-image: radial-gradient(closest-side, rgba(255,255,255,.6), rgba(255,255,255,0)), radial-gradient(closest-side, rgba(255,190,232,.5), rgba(255,190,232,0)), radial-gradient(closest-side, rgba(170,215,255,.55), rgba(170,215,255,0)) !important;
    animation: sxBlob 9s ease-in-out infinite alternate !important; }
html body .sx-hero.sx-hero::after { animation-duration: 12s !important; }
html body .sx-stat.sx-stat::after { display: block !important; animation-duration: 10s !important; }
html body .sx-stat.sx-stat:nth-child(2n)::after { animation-delay: -3s !important; } html body .sx-stat.sx-stat:nth-child(3n)::after { animation-delay: -6s !important; }

/* kilau diagonal kaca yang menyapu isi kartu */
html body .sx-level.sx-level::after, html body .sx-hero.sx-hero::before, html body .sx-stat.sx-stat::before {
    background-size: 280% 100%, 100% 100%, 100% 100% !important;
    background-image: linear-gradient(112deg, rgba(255,255,255,0) 38%, rgba(255,255,255,.28) 43%, rgba(255,255,255,0) 47%, rgba(255,255,255,0) 52%, rgba(255,214,240,.5) 56%, rgba(255,255,255,.95) 59%, rgba(170,215,255,.55) 62%, rgba(255,255,255,0) 67%), linear-gradient(180deg, rgba(255,255,255,.6) 0%, rgba(255,255,255,.12) 24%, rgba(255,255,255,0) 46%), linear-gradient(0deg, rgba(255,255,255,.22) 0%, rgba(255,255,255,0) 30%) !important;
    animation: sxStreak 6.5s ease-in-out infinite !important; }
@media (prefers-reduced-motion: reduce) { html body .sx-level.sx-level::before, html body .sx-hero.sx-hero::after, html body .sx-stat.sx-stat::after, html body .sx-level.sx-level::after, html body .sx-hero.sx-hero::before, html body .sx-stat.sx-stat::before { animation: none !important; } }
</style>
