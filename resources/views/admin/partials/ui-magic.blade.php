<style>
    @property --ang { syntax:'<angle>'; inherits:false; initial-value:0deg; }
    .ab { --star:polygon(50% 0,61% 39%,100% 50%,61% 61%,50% 100%,39% 61%,0 50%,39% 39%); }
    @keyframes abSpin { to { --ang:360deg; } }
    @keyframes abHue { to { background-position:250% 0; } }
    @keyframes abDriftA { 0%,100% { transform:translate(0,0) scale(1); } 50% { transform:translate(70px,50px) scale(1.18); } }
    @keyframes abDriftB { 0%,100% { transform:translate(0,0) scale(1); } 50% { transform:translate(-60px,70px) scale(1.12); } }
    @keyframes abTwinkle { 0%,100% { opacity:0; transform:scale(.3) rotate(0deg); } 50% { opacity:.95; transform:scale(1) rotate(45deg); } }
    @keyframes abPulse { 0%,100% { transform:scale(.5) rotate(0deg); opacity:.5; } 50% { transform:scale(1.15) rotate(90deg); opacity:1; } }
    @keyframes abPop { from { opacity:0; transform:translateY(18px) scale(.94); filter:blur(8px); } to { opacity:1; transform:none; filter:none; } }
    @keyframes abBurst { from { opacity:1; transform:translate(0,0) scale(.4); } to { opacity:0; transform:translate(var(--dx),var(--dy)) scale(1.3) rotate(180deg); } }
    @keyframes abCover { from { opacity:0; transform:scale(1.2); filter:blur(10px) brightness(1.6); } to { opacity:1; transform:none; filter:none; } }

    .ab::before { animation:abDriftA 16s ease-in-out infinite; width:520px; height:380px; }
    .ab::after { animation:abDriftB 19s ease-in-out infinite; width:480px; height:380px; }
    .ab-sparks { position:absolute; left:0; right:0; top:0; height:660px; z-index:-1; pointer-events:none; overflow:hidden; }
    .ab-sparks i { position:absolute; clip-path:var(--star); background:linear-gradient(135deg,#fff,#ffd3ea 45%,#c9b6ff); opacity:0; animation:abTwinkle ease-in-out infinite; }

    .ab-h1 { font-size:clamp(2.2rem,4.6vw,3.4rem); }
    .ab-h1 em { background:linear-gradient(100deg,#7c5ce0,#f06fb0,#ffb86b,#f06fb0,#7c5ce0); background-size:250% 100%; -webkit-background-clip:text; background-clip:text; color:transparent; animation:abHue 7s linear infinite; }
    .ab-btn.primary { position:relative; overflow:hidden; }
    .ab-btn.primary::after { content:''; position:absolute; top:0; left:-70%; width:40%; height:100%; background:linear-gradient(100deg,transparent,rgba(255,255,255,.6),transparent); transform:skewX(-20deg); transition:left .7s ease; }
    .ab-btn.primary:hover::after { left:140%; }

    /* ---- kolom ISBN: bingkai sihir berputar ---- */
    .ab-spot { position:relative; padding:3px; border-radius:999px; background:conic-gradient(from var(--ang),#7c5ce0,#f06fb0,#ffc97a,#2fc79d,#7c5ce0); animation:abSpin 7s linear infinite; box-shadow:0 0 0 7px rgba(255,255,255,.35), 0 0 70px rgba(240,111,176,.4), 0 30px 60px rgba(124,92,224,.3); }
    .ab-spot.busy { animation-duration:1.5s; }
    .ab-spot form { padding:10px 10px 10px 28px; border-radius:999px; background:linear-gradient(180deg,#fff,#fcf9ff); }
    .ab .ab-spot input.ab-isbn-in { height:58px; padding:0 !important; border:0 !important; outline:0 !important; box-shadow:none !important; border-radius:0 !important; background:transparent !important; font-size:1.4rem; }
    .ab-spot .ab-btn { height:58px; }
    .ab-magic { display:flex; align-items:center; gap:12px; margin:18px 10px 0; font:600 .8rem 'Poppins',sans-serif; color:var(--acc); }
    .ab-magic .st { display:flex; gap:6px; }
    .ab-magic .st i { width:15px; height:15px; clip-path:var(--star); background:linear-gradient(135deg,#7c5ce0,#f06fb0); animation:abPulse 1s ease-in-out infinite; }
    .ab-magic .st i:nth-child(2) { animation-delay:.2s; } .ab-magic .st i:nth-child(3) { animation-delay:.4s; }

    /* ---- panel organik, bukan kotak kaku ---- */
    .ab-form .ab-sec { padding:28px 28px 30px; border-radius:48px 18px 48px 18px; background:linear-gradient(150deg,rgba(255,255,255,.8),rgba(255,255,255,.34)); border:1px solid rgba(255,255,255,.95); box-shadow:0 24px 54px rgba(124,92,224,.13), inset 0 1px 0 rgba(255,255,255,.95); backdrop-filter:blur(14px); -webkit-backdrop-filter:blur(14px); }
    .ab-form .ab-sec + .ab-sec { margin-top:22px; border-radius:18px 48px 18px 48px; }
    .ab-form .ab-sec + .ab-sec + .ab-sec { border-radius:48px 18px 48px 18px; }
    .ab-f input, .ab-f select, .ab-f textarea { border-radius:20px; }
    .ab-f input:focus, .ab-f select:focus, .ab-f textarea:focus { box-shadow:0 0 0 4px rgba(124,92,224,.16), 0 12px 30px rgba(124,92,224,.24); }
    .ab-f textarea.ab-missing { background-color:rgba(240,160,48,.12); border-color:rgba(240,160,48,.55); }
    .ab-f.pop { animation:abPop .7s cubic-bezier(.2,.9,.3,1.25) both; }

    /* ---- panel pratinjau + buku 3D ---- */
    .ab-side { border-radius:62px 22px 62px 22px; background:radial-gradient(120% 80% at 20% 0%,rgba(255,255,255,.96),rgba(255,255,255,.46)); box-shadow:0 30px 70px rgba(124,92,224,.2), inset 0 1px 0 #fff; }
    .ab-stage { isolation:isolate; animation:abFloat 6s ease-in-out infinite; }
    .ab-stage::before { content:''; position:absolute; inset:-26px; z-index:-1; border-radius:50%; background:conic-gradient(from var(--ang),rgba(124,92,224,.55),rgba(240,111,176,.55),rgba(255,210,122,.5),rgba(47,199,157,.4),rgba(124,92,224,.55)); filter:blur(30px); animation:abSpin 9s linear infinite; }
    .ab .ab-book { animation:none; transform:rotateY(var(--ry,-18deg)) rotateX(var(--rx,3deg)); transition:transform .18s ease-out; }
    .ab .ab-stage:hover .ab-book { transform:rotateY(var(--ry,-18deg)) rotateX(var(--rx,3deg)); }
    .ab-book::after { background:radial-gradient(circle at var(--gx,25%) var(--gy,15%),rgba(255,255,255,.62),transparent 52%), linear-gradient(115deg,rgba(255,255,255,.2),transparent 40%); }
    .ab-book img.in { animation:abCover .9s cubic-bezier(.2,.8,.2,1); }
    .ab-burst { position:absolute; left:50%; top:50%; width:15px; height:15px; margin:-7px; z-index:5; pointer-events:none; clip-path:var(--star); background:linear-gradient(135deg,#fff,#ffd27a); animation:abBurst .9s ease-out forwards; }

    /* ---- bar simpan: diam di bawah, tidak menimpa isian ---- */
    .ab-form { padding-bottom:8px; }
    .ab-dock { position:sticky; bottom:14px; margin-top:30px; padding:10px 10px 10px 24px; border-radius:999px; background:rgba(255,255,255,.92); box-shadow:0 22px 54px rgba(60,40,140,.32), inset 0 0 0 1px rgba(255,255,255,.9); }
    .ab-dock::before { content:''; position:absolute; z-index:-1; left:-34px; right:-34px; top:-40px; bottom:-16px; pointer-events:none; backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px); background:linear-gradient(to top,rgba(247,241,255,.94),rgba(247,241,255,0)); -webkit-mask-image:linear-gradient(to top,#000 55%,transparent); mask-image:linear-gradient(to top,#000 55%,transparent); }
    .ab-dock .ab-btn.primary { height:50px; padding:0 2rem; font-size:.86rem; }

    @media (max-width:640px) { .ab-form .ab-sec { padding:22px 18px 24px; border-radius:34px 14px 34px 14px; } .ab-form .ab-sec + .ab-sec { border-radius:14px 34px 14px 34px; } .ab-dock::before { left:-14px; right:-14px; } }
    @media (prefers-reduced-motion:reduce) { .ab-sparks { display:none; } }
</style>
<script>
    (function () {
        var root = document.querySelector('.ab');
        if (!root || root.querySelector('.ab-sparks') || window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }
        var wrap = document.createElement('div');
        wrap.className = 'ab-sparks';
        wrap.setAttribute('aria-hidden', 'true');
        for (var i = 0; i < 24; i++) {
            var s = document.createElement('i'), z = 8 + Math.random() * 18;
            s.style.cssText = 'left:' + (Math.random() * 100) + '%;top:' + (Math.random() * 620) + 'px;width:' + z + 'px;height:' + z + 'px;animation-delay:' + (Math.random() * 5) + 's;animation-duration:' + (3 + Math.random() * 4) + 's';
            wrap.appendChild(s);
        }
        root.insertBefore(wrap, root.firstChild);
    })();
</script>