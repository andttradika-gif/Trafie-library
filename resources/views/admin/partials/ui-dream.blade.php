<style>
    @keyframes abBob { 0%,100% { transform:translateY(0) rotate(var(--r,0deg)); } 50% { transform:translateY(-36px) rotate(calc(var(--r,0deg) + 12deg)); } }
    @keyframes abRay { 0%,100% { opacity:.22; transform:rotate(24deg) translateX(0); } 50% { opacity:.62; transform:rotate(24deg) translateX(70px); } }
    @keyframes abAura { 0%,100% { opacity:.7; transform:scale(.97); } 50% { opacity:1; transform:scale(1.05); } }
    @keyframes abScan { 0%,100% { top:3px; } 50% { top:29px; } }
    @keyframes abGloss { 0%,60% { left:-60%; } 100% { left:140%; } }

    /* ---------- latar mimpi ---------- */
    .ab-sparks { display:none !important; }
    .ab-dream { position:absolute; left:-140px; right:-140px; top:-40px; bottom:-80px; z-index:-1; pointer-events:none; }
    .ab-dream > i { position:absolute; display:block; }
    .ab-orb { border-radius:50%; filter:blur(70px); opacity:.55; animation:abDriftA ease-in-out infinite; }
    .ab-ray { width:200px; height:140%; top:-20%; background:linear-gradient(to bottom,rgba(255,255,255,0),rgba(255,255,255,.75) 40%,rgba(255,214,240,.35) 70%,rgba(255,255,255,0)); filter:blur(26px); transform:rotate(24deg); opacity:.5; animation:abRay 13s ease-in-out infinite; }
    .ab-dia { clip-path:polygon(25% 0,75% 0,100% 35%,50% 100%,0 35%); background:linear-gradient(135deg,rgba(255,255,255,.95),rgba(215,200,255,.5) 40%,rgba(255,190,225,.55) 75%,rgba(255,255,255,.8)); animation:abBob ease-in-out infinite; }
    .ab-dia::after { content:''; position:absolute; inset:0; background:linear-gradient(to bottom,transparent 34%,rgba(255,255,255,.9) 35%,transparent 37%), linear-gradient(62deg,transparent 48.5%,rgba(255,255,255,.75) 50%,transparent 51.5%), linear-gradient(-62deg,transparent 48.5%,rgba(255,255,255,.75) 50%,transparent 51.5%); }
    .ab-st { clip-path:var(--star); background:linear-gradient(135deg,#fff,#ffd3ea 45%,#c9b6ff); opacity:0; animation:abTwinkle ease-in-out infinite; }
    .ab-gl { border-radius:50%; background:#fff; box-shadow:0 0 8px 2px rgba(255,255,255,.95), 0 0 16px rgba(240,111,176,.55); opacity:0; animation:abTwinkle ease-in-out infinite; }
    .ab-bub { border-radius:50%; background:radial-gradient(circle at 30% 28%,rgba(255,255,255,.95),rgba(255,255,255,.25) 38%,rgba(190,170,255,.28) 72%,rgba(255,190,225,.32)); border:1px solid rgba(255,255,255,.8); box-shadow:inset 0 -8px 18px rgba(255,255,255,.45), 0 12px 32px rgba(124,92,224,.2); animation:abBob ease-in-out infinite; }

    /* ---------- kolom ISBN: kapsul kristal ---------- */
    .ab-spot { margin-top:6px; box-shadow:0 0 0 8px rgba(255,255,255,.3), 0 0 80px rgba(240,111,176,.4), 0 34px 70px rgba(124,92,224,.32); }
    .ab-spot::before { content:''; position:absolute; inset:-30px; z-index:-1; border-radius:999px; background:radial-gradient(closest-side,rgba(240,111,176,.5),rgba(124,92,224,.28) 60%,transparent); filter:blur(26px); animation:abAura 3.6s ease-in-out infinite; }
    .ab-spot form { position:relative; padding:12px 12px 12px 30px; gap:16px; background:linear-gradient(135deg,rgba(255,255,255,.98),rgba(246,238,255,.93) 55%,rgba(255,236,247,.96)); box-shadow:inset 0 2px 0 #fff, inset 0 -12px 26px rgba(180,150,255,.2); }
    .ab-spot form::before { content:''; position:absolute; left:26px; right:26px; top:7px; height:36%; border-radius:999px; background:linear-gradient(to bottom,rgba(255,255,255,.95),rgba(255,255,255,0)); pointer-events:none; }
    .ab-spot form > * { position:relative; }
    .ab-spot .bc { width:36px; height:36px; flex:none; display:flex; align-items:center; justify-content:center; }
    .ab-spot .bc svg { width:32px; height:32px; fill:none; stroke:var(--acc); stroke-width:1.8; stroke-linecap:round; }
    .ab-spot .bc .beam { position:absolute; left:-3px; right:-3px; top:3px; height:2px; border-radius:2px; background:linear-gradient(90deg,transparent,#f06fb0,transparent); box-shadow:0 0 12px #f06fb0; animation:abScan 2.2s ease-in-out infinite; }
    .ab .ab-spot form input#isbn.ab-isbn-in { all:unset !important; box-sizing:border-box !important; flex:1 1 auto !important; min-width:0 !important; height:62px !important; font:600 1.5rem 'Poppins',sans-serif !important; letter-spacing:.09em !important; background:linear-gradient(90deg,#4b3a9a,#c0408f) !important; -webkit-background-clip:text !important; background-clip:text !important; -webkit-text-fill-color:transparent !important; caret-color:#f06fb0 !important; cursor:text !important; }
    .ab .ab-spot form input#isbn.ab-isbn-in::placeholder { -webkit-text-fill-color:#b9b4d6 !important; font-weight:500; letter-spacing:.03em; font-size:1.05rem; }
    .ab-spot .ab-btn.go { height:62px; padding:0 2.1rem; font-size:.9rem; background:linear-gradient(120deg,#7c5ce0,#c060d8 45%,#f06fb0 80%,#ffa8cf); box-shadow:0 14px 30px rgba(240,111,176,.45), inset 0 1px 0 rgba(255,255,255,.5); }
    .ab-spot .ab-btn.go svg { fill:currentColor; stroke:none; width:19px; height:19px; animation:abPulse 2.2s ease-in-out infinite; }
    .ab-gem { position:absolute; z-index:3; width:20px; height:20px; clip-path:var(--star); background:linear-gradient(135deg,#fff,#ffd27a); animation:abTwinkle 3.2s ease-in-out infinite; pointer-events:none; }
    .ab-gem.g1 { left:-12px; top:-8px; } .ab-gem.g2 { right:14%; top:-16px; width:14px; height:14px; animation-delay:.9s; }
    .ab-gem.g3 { left:32%; bottom:-14px; width:16px; height:16px; animation-delay:1.7s; } .ab-gem.g4 { right:-10px; bottom:6px; width:24px; height:24px; animation-delay:2.3s; }

    /* ---------- panel kaca berkilau ---------- */
    .ab-form .ab-sec { position:relative; overflow:hidden; }
    .ab-form .ab-sec::after { content:''; position:absolute; top:-60%; left:-60%; width:36%; height:220%; background:linear-gradient(100deg,transparent,rgba(255,255,255,.55),transparent); transform:rotate(18deg); animation:abGloss 10s ease-in-out infinite; pointer-events:none; }
    .ab-form .ab-sec > * { position:relative; z-index:1; }
    .ab-side { overflow:hidden; }
    .ab-side::after { content:''; position:absolute; top:-50%; left:-70%; width:40%; height:200%; background:linear-gradient(100deg,transparent,rgba(255,255,255,.6),transparent); transform:rotate(18deg); animation:abGloss 12s ease-in-out infinite; pointer-events:none; }

    /* ---------- bar simpan: pulau kristal melayang ---------- */
    .ab-dock { width:fit-content; max-width:100%; margin:30px auto 0; padding:9px 9px 9px 14px; gap:18px; background:linear-gradient(135deg,rgba(255,255,255,.96),rgba(250,240,255,.9)); backdrop-filter:blur(18px); -webkit-backdrop-filter:blur(18px); box-shadow:inset 0 1px 0 #fff, 0 26px 56px rgba(60,40,140,.32), 0 0 46px rgba(240,111,176,.3); }
    .ab-dock::before { content:''; position:absolute; left:-2px; right:-2px; top:-2px; bottom:-2px; z-index:auto; padding:2px; border-radius:inherit; pointer-events:none; backdrop-filter:none; -webkit-backdrop-filter:none; background:conic-gradient(from var(--ang),#7c5ce0,#f06fb0,#ffc97a,#2fc79d,#7c5ce0); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite:xor; mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); mask-composite:exclude; animation:abSpin 8s linear infinite; }
    .ab-dock .ab-acts { padding-left:16px; border-left:1px solid rgba(124,92,224,.2); }
    .ab-toggle { position:relative; display:flex; align-items:center; gap:.75rem; padding:4px 6px; font:500 .76rem 'Poppins',sans-serif; color:#55517c; cursor:pointer; user-select:none; }
    .ab-toggle input { position:absolute; opacity:0; pointer-events:none; }
    .ab-toggle .tg { position:relative; flex:none; width:48px; height:28px; border-radius:99px; background:rgba(124,92,224,.16); box-shadow:inset 0 2px 6px rgba(60,40,140,.2); transition:background .25s, box-shadow .25s; }
    .ab-toggle .tg::after { content:''; position:absolute; top:3px; left:3px; width:22px; height:22px; border-radius:50%; background:linear-gradient(135deg,#fff,#efe7ff); box-shadow:0 4px 10px rgba(60,40,140,.32); transition:transform .3s cubic-bezier(.3,1.4,.5,1); }
    .ab-toggle input:checked + .tg { background:linear-gradient(120deg,#7c5ce0,#f06fb0); box-shadow:inset 0 2px 6px rgba(60,40,140,.2), 0 0 20px rgba(240,111,176,.6); }
    .ab-toggle input:checked + .tg::after { transform:translateX(20px); }
    .ab-toggle input:focus-visible + .tg { outline:2px solid var(--acc); outline-offset:2px; }

    @media (max-width:640px) {
        .ab-dock { width:100%; border-radius:30px; padding:12px; gap:12px; }
        .ab-dock .ab-acts { width:100%; padding-left:0; border-left:0; }
        .ab-spot form { padding-left:18px; gap:10px; } .ab-spot .bc { display:none; }
        .ab .ab-spot form input#isbn.ab-isbn-in { font-size:1.15rem !important; height:54px !important; }
        .ab-spot .ab-btn.go { height:54px; padding:0 1.2rem; }
    }
    @media (prefers-reduced-motion:reduce) { .ab-dream { display:none; } }
</style>
<script>
    (function () {
        var root = document.querySelector('.ab');
        if (!root || root.querySelector('.ab-dream') || window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }
        var old = root.querySelector('.ab-sparks'); if (old) { old.remove(); }
        var H = Math.max(root.offsetHeight, 900), r = Math.random;
        var box = document.createElement('div');
        box.className = 'ab-dream'; box.setAttribute('aria-hidden', 'true');
        function add(cls, css) { var e = document.createElement('i'); e.className = cls; e.style.cssText = css; box.appendChild(e); }
        function pct(a, b) { return (a + r() * (b - a)).toFixed(1) + '%'; }
        var cols = ['rgba(240,111,176,.8)', 'rgba(124,92,224,.75)', 'rgba(255,196,122,.7)', 'rgba(47,199,157,.55)', 'rgba(120,170,255,.7)'];
        var i, s, nO = Math.min(8, Math.ceil(H / 480));

        for (i = 0; i < nO; i++) {
            s = 380 + r() * 260;
            add('ab-orb', 'width:' + s + 'px;height:' + s + 'px;left:' + pct(-6, 86) + ';top:' + (i * (100 / nO) + r() * 6).toFixed(1) + '%;background:radial-gradient(closest-side,' + cols[i % 5] + ',transparent);animation-duration:' + (14 + r() * 10) + 's;animation-delay:-' + (r() * 10) + 's');
        }
        add('ab-ray', 'left:18%'); add('ab-ray', 'left:64%;animation-delay:-6s');

        var nD = Math.min(22, Math.ceil(H / 240) + 4);
        for (i = 0; i < nD; i++) {
            s = 26 + r() * 84;
            add('ab-dia', 'width:' + s + 'px;height:' + s + 'px;left:' + pct(-2, 96) + ';top:' + pct(0, 96) + ';opacity:' + (0.35 + r() * 0.4).toFixed(2) + ';--r:' + (r() * 40 - 20).toFixed(0) + 'deg;animation-duration:' + (9 + r() * 8) + 's;animation-delay:-' + (r() * 8) + 's');
        }
        var nS = Math.min(44, Math.ceil(H / 36));
        for (i = 0; i < nS; i++) {
            s = 8 + r() * 18;
            add('ab-st', 'width:' + s + 'px;height:' + s + 'px;left:' + pct(0, 98) + ';top:' + pct(0, 98) + ';animation-duration:' + (3 + r() * 4) + 's;animation-delay:' + (r() * 5) + 's');
        }
        var nG = Math.min(90, Math.ceil(H / 14));
        for (i = 0; i < nG; i++) {
            s = 2 + r() * 3;
            add('ab-gl', 'width:' + s + 'px;height:' + s + 'px;left:' + pct(0, 99) + ';top:' + pct(0, 99) + ';animation-duration:' + (2.4 + r() * 4) + 's;animation-delay:' + (r() * 6) + 's');
        }
        var nB = Math.min(12, Math.ceil(H / 170));
        for (i = 0; i < nB; i++) {
            s = 24 + r() * 60;
            add('ab-bub', 'width:' + s + 'px;height:' + s + 'px;left:' + pct(0, 96) + ';top:' + pct(0, 96) + ';opacity:' + (0.5 + r() * 0.4).toFixed(2) + ';--r:0deg;animation-duration:' + (11 + r() * 9) + 's;animation-delay:-' + (r() * 10) + 's');
        }
        root.insertBefore(box, root.firstChild);
    })();
</script>