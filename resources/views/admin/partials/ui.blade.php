<style>
    .ab { --ink:#241f4a; --mut:#7a7699; --line:rgba(140,118,230,.2); --acc:#7c5ce0; --acc2:#f06fb0; --mint:#2fc79d; --amber:#f0a030; --bad:#e0405f;
        position:relative; isolation:isolate; max-width:1240px; margin:0 auto; display:flex; flex-direction:column; gap:28px; color:var(--ink); font-family:'Poppins',sans-serif; }
    .ab *, .ab *::before, .ab *::after { box-sizing:border-box; }
    .ab [hidden] { display:none !important; }
    .ab::before, .ab::after { content:''; position:absolute; z-index:-1; pointer-events:none; border-radius:50%; filter:blur(44px); }
    .ab::before { top:-70px; left:-90px; width:420px; height:300px; background:radial-gradient(closest-side, rgba(240,111,176,.38), transparent); }
    .ab::after { top:20px; right:-60px; width:380px; height:300px; background:radial-gradient(closest-side, rgba(124,92,224,.32), transparent); }

    .ab-top { display:flex; align-items:flex-end; justify-content:space-between; gap:20px; flex-wrap:wrap; }
    .ab-eye { display:inline-flex; align-items:center; gap:.5rem; padding:.32rem .85rem; border-radius:99px; background:rgba(255,255,255,.7); border:1px solid var(--line); font:600 .62rem 'Poppins',sans-serif; letter-spacing:.16em; text-transform:uppercase; color:var(--acc); backdrop-filter:blur(8px); }
    .ab-eye i { width:7px; height:7px; border-radius:50%; background:linear-gradient(135deg,var(--acc),var(--acc2)); box-shadow:0 0 0 3px rgba(124,92,224,.18); }
    .ab-h1 { margin:.7rem 0 0; font:700 clamp(2rem,4.2vw,3.1rem)/1.04 'Poppins',sans-serif; letter-spacing:-.04em; color:var(--ink); }
    .ab-h1 em { font:italic 600 1.06em 'Playfair Display',serif; letter-spacing:-.01em; background:linear-gradient(120deg,var(--acc),var(--acc2)); -webkit-background-clip:text; background-clip:text; color:transparent; padding-right:.1em; }
    .ab-sub { margin:.7rem 0 0; max-width:540px; font-size:.82rem; line-height:1.65; color:var(--mut); }
    .ab-acts { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }

    .ab-btn { display:inline-flex; align-items:center; justify-content:center; gap:.55rem; height:46px; padding:0 1.4rem; border-radius:999px; border:1px solid transparent; font:600 .8rem 'Poppins',sans-serif; text-decoration:none; cursor:pointer; white-space:nowrap; transition:transform .18s ease, box-shadow .18s ease, background .18s ease; }
    .ab-btn svg { width:17px; height:17px; fill:none; stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    .ab-btn.primary { color:#fff; background:linear-gradient(120deg,#7c5ce0,#b560d8 55%,#f06fb0); box-shadow:0 12px 26px rgba(124,92,224,.34), inset 0 1px 0 rgba(255,255,255,.35); }
    .ab-btn.primary:hover { transform:translateY(-2px); box-shadow:0 18px 34px rgba(124,92,224,.44), inset 0 1px 0 rgba(255,255,255,.35); }
    .ab-btn.ghost { color:var(--acc); background:rgba(255,255,255,.75); border-color:var(--line); backdrop-filter:blur(8px); }
    .ab-btn.ghost:hover { background:#fff; transform:translateY(-1px); }
    .ab-btn.danger { color:var(--bad); background:rgba(255,255,255,.75); border-color:rgba(224,64,95,.3); }
    .ab-btn.danger:hover { background:rgba(224,64,95,.1); }
    .ab-btn.sm { height:36px; padding:0 1rem; font-size:.72rem; }
    .ab-btn[disabled] { opacity:.65; cursor:wait; transform:none; }
    .ab-link { align-self:flex-start; padding:0; border:0; background:none; font:500 .76rem 'Poppins',sans-serif; color:var(--acc); text-decoration:underline; text-underline-offset:4px; cursor:pointer; }

    .ab-f { position:relative; }
    .ab-f input, .ab-f select, .ab-f textarea { width:100%; height:58px; padding:24px 16px 8px; border-radius:18px; border:1.5px solid transparent; background:rgba(124,92,224,.075); font:500 .86rem 'Poppins',sans-serif; color:var(--ink); outline:none; transition:background .18s, border-color .18s, box-shadow .18s; appearance:none; -webkit-appearance:none; }
    .ab-f textarea { height:auto; min-height:150px; padding-top:30px; line-height:1.65; resize:vertical; }
    .ab-f select { cursor:pointer; padding-right:40px; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%237c5ce0' stroke-width='2.4' stroke-linecap='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 16px center; }
    .ab-f input:hover, .ab-f select:hover, .ab-f textarea:hover { background-color:rgba(124,92,224,.11); }
    .ab-f input:focus, .ab-f select:focus, .ab-f textarea:focus { background-color:#fff; border-color:var(--acc); box-shadow:0 0 0 4px rgba(124,92,224,.16); }
    .ab-f label { position:absolute; left:17px; top:19px; font:500 .84rem 'Poppins',sans-serif; color:var(--mut); pointer-events:none; transform-origin:left top; transition:transform .16s ease, color .16s ease; }
    .ab-f input:focus ~ label, .ab-f input:not(:placeholder-shown) ~ label,
    .ab-f textarea:focus ~ label, .ab-f textarea:not(:placeholder-shown) ~ label,
    .ab-f select ~ label { transform:translateY(-12px) scale(.74); color:var(--acc); font-weight:600; }
    .ab-f input.ab-missing, .ab-f select.ab-missing { background-color:rgba(240,160,48,.12); border-color:rgba(240,160,48,.55); }
    .ab-f .ab-missing ~ label::after { content:' \2022 isi manual'; color:var(--amber); font-weight:600; }
    .ab-note { margin:7px 4px 0; font-size:.68rem; line-height:1.5; color:var(--mut); }
    .ab-file { display:block; width:100%; padding:10px; border-radius:18px; border:1.5px dashed rgba(124,92,224,.35); background:rgba(255,255,255,.5); font:500 .76rem 'Poppins',sans-serif; color:var(--mut); cursor:pointer; }
    .ab-file::file-selector-button { margin-right:12px; padding:.55rem 1.1rem; border:0; border-radius:99px; background:linear-gradient(120deg,#7c5ce0,#f06fb0); color:#fff; font:600 .72rem 'Poppins',sans-serif; cursor:pointer; }
    .ab-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; }
    .ab-grid .full { grid-column:1 / -1; }

    .ab-sec { display:flex; flex-direction:column; gap:14px; }
    .ab-sec + .ab-sec { margin-top:30px; }
    .ab-sec > h2 { margin:0; display:flex; align-items:center; gap:.8rem; font:600 .68rem 'Poppins',sans-serif; letter-spacing:.18em; text-transform:uppercase; color:var(--mut); }
    .ab-sec > h2::after { content:''; flex:1; height:1px; background:linear-gradient(90deg,var(--line),transparent); }
    .ab-sec > h2 b { display:inline-flex; width:24px; height:24px; align-items:center; justify-content:center; border-radius:8px; background:linear-gradient(135deg,var(--acc),var(--acc2)); color:#fff; font-size:.7rem; letter-spacing:0; }

    .ab-chips { display:flex; flex-wrap:wrap; gap:6px; }
    .ab-chip { display:inline-flex; align-items:center; gap:.35rem; padding:.28rem .75rem; border-radius:99px; font:500 .66rem 'Poppins',sans-serif; background:rgba(124,92,224,.12); color:#5b3fc4; }
    .ab-chip.mono { font-family:ui-monospace,Menlo,Consolas,monospace; background:rgba(36,31,74,.07); color:#5d5983; }
    .ab-chip.ok { background:rgba(47,199,157,.16); color:#13795a; }
    .ab-chip.src { background:rgba(61,150,230,.15); color:#1f68a8; }
    .ab-msg { padding:13px 16px; border-radius:18px; font-size:.78rem; line-height:1.6; }
    .ab-msg.err { background:rgba(224,64,95,.12); color:#a5304a; } .ab-msg.warn { background:rgba(240,160,48,.16); color:#86550c; } .ab-msg.ok { background:rgba(47,199,157,.16); color:#13795a; }
    .ab-msg ul { margin:.4rem 0 0 1.1rem; padding:0; } .ab-msg a { color:inherit; font-weight:600; }

    .ab-toast { position:fixed; right:22px; bottom:22px; z-index:90; max-width:360px; padding:14px 20px; border-radius:20px; font:500 .78rem/1.5 'Poppins',sans-serif; box-shadow:0 18px 40px rgba(60,40,140,.26); animation:abToast .4s cubic-bezier(.2,.9,.3,1.2) both; }
    .ab-toast.ok { background:linear-gradient(120deg,#1fb98c,#35d3a6); color:#fff; } .ab-toast.err { background:linear-gradient(120deg,#d6405b,#f0607a); color:#fff; } .ab-toast.warn { background:linear-gradient(120deg,#e8962e,#f5b04f); color:#fff; }
    .ab-toast + .ab-toast { bottom:92px; }
    @keyframes abToast { from { opacity:0; transform:translateY(18px) scale(.96); } to { opacity:1; transform:none; } }
    @keyframes abRise { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:none; } }
    @keyframes abFloat { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-8px); } }
    @keyframes abShimmer { from { background-position:200% 0; } to { background-position:-200% 0; } }

    .ab-split { display:grid; grid-template-columns:340px minmax(0,1fr); gap:44px; align-items:start; }
    .ab-side { position:sticky; top:96px; display:flex; flex-direction:column; align-items:center; gap:18px; padding:30px 22px 26px; border-radius:36px 36px 36px 10px; background:linear-gradient(160deg,rgba(255,255,255,.85),rgba(255,255,255,.5)); border:1px solid rgba(255,255,255,.95); box-shadow:0 24px 60px rgba(124,92,224,.16); backdrop-filter:blur(14px); -webkit-backdrop-filter:blur(14px); }
    .ab-stage { position:relative; width:200px; padding:6px 0 22px; perspective:900px; }
    .ab-book { position:relative; width:100%; aspect-ratio:2/3; border-radius:6px 14px 14px 6px; transform:rotateY(-18deg) rotateX(3deg); transform-style:preserve-3d; transition:transform .5s cubic-bezier(.2,.8,.2,1); animation:abFloat 5.5s ease-in-out infinite; box-shadow:-10px 18px 34px rgba(60,40,140,.35), 0 2px 0 rgba(255,255,255,.4) inset; background:linear-gradient(135deg,#8f78d8,#f06fb0); overflow:hidden; display:flex; align-items:center; justify-content:center; text-align:center; padding:14px; font:700 .95rem/1.25 'Playfair Display',serif; color:#fff; }
    .ab-stage:hover .ab-book { transform:rotateY(-4deg) rotateX(0); }
    .ab-book::before { content:''; position:absolute; inset:0 auto 0 0; width:9px; background:linear-gradient(90deg,rgba(0,0,0,.28),rgba(255,255,255,.18),rgba(0,0,0,.06)); z-index:2; }
    .ab-book::after { content:''; position:absolute; inset:0; background:linear-gradient(115deg,rgba(255,255,255,.28),transparent 42%); z-index:2; pointer-events:none; }
    .ab-book img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
    .ab-shadow { position:absolute; left:12%; right:6%; bottom:6px; height:16px; border-radius:50%; background:rgba(60,40,140,.28); filter:blur(11px); }
    .ab-pv { width:100%; text-align:center; }
    .ab-pv h3 { margin:0; font:700 1.05rem/1.3 'Poppins',sans-serif; letter-spacing:-.01em; }
    .ab-pv p { margin:.25rem 0 .8rem; font-size:.76rem; color:var(--mut); }
    .ab-pv .ab-chips { justify-content:center; }
    .ab-pv q { display:block; margin-top:.9rem; font:italic 400 .76rem/1.6 'Playfair Display',serif; color:#6a6690; quotes:none; }
    .ab-flow { min-width:0; display:flex; flex-direction:column; gap:26px; }

    .ab-spot { padding:2px; border-radius:34px; background:linear-gradient(120deg,var(--acc),var(--acc2),var(--mint)); box-shadow:0 24px 56px rgba(124,92,224,.26); }
    .ab-spot form { display:flex; align-items:center; gap:10px; padding:9px 9px 9px 24px; border-radius:32px; background:#fff; }
    .ab-spot svg.bc { width:26px; height:26px; flex-shrink:0; fill:none; stroke:var(--acc); stroke-width:1.7; stroke-linecap:round; }
    .ab-spot input { flex:1; min-width:0; height:54px; border:0; outline:0; background:transparent; font:500 1.28rem ui-monospace,Menlo,Consolas,monospace; letter-spacing:.07em; color:var(--ink); }
    .ab-spot input::placeholder { color:#b9b4d6; letter-spacing:.04em; }
    .ab-spot .ab-btn { height:54px; padding:0 1.9rem; font-size:.86rem; }
    .ab-help { margin:12px 6px 0; font-size:.72rem; line-height:1.65; color:var(--mut); }
    .ab-skel { display:flex; gap:12px; margin-top:14px; } .ab-skel i { height:12px; border-radius:99px; flex:1; background:linear-gradient(90deg,#ece6fb 25%,#faf7ff 50%,#ece6fb 75%); background-size:200% 100%; animation:abShimmer 1.1s linear infinite; }

    .ab-form { display:flex; flex-direction:column; animation:abRise .5s ease both; }
    .ab-dock { position:sticky; bottom:16px; z-index:20; display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap; margin-top:34px; padding:12px 12px 12px 22px; border-radius:999px; background:rgba(255,255,255,.82); border:1px solid rgba(255,255,255,.95); box-shadow:0 18px 46px rgba(60,40,140,.22); backdrop-filter:blur(16px); -webkit-backdrop-filter:blur(16px); }
    .ab-check { display:flex; align-items:center; gap:.6rem; font:500 .75rem 'Poppins',sans-serif; color:#55517c; cursor:pointer; }
    .ab-check input { width:18px; height:18px; accent-color:var(--acc); }

    .ab-bento { display:grid; grid-template-columns:1.7fr 1fr 1fr 1fr; gap:14px; }
    .ab-tile { position:relative; overflow:hidden; display:flex; flex-direction:column; justify-content:space-between; min-height:132px; padding:20px 22px; border-radius:30px 30px 30px 10px; background:rgba(255,255,255,.78); border:1px solid rgba(255,255,255,.95); box-shadow:0 12px 32px rgba(124,92,224,.1); backdrop-filter:blur(12px); }
    .ab-tile span { font:600 .64rem 'Poppins',sans-serif; letter-spacing:.14em; text-transform:uppercase; color:var(--mut); }
    .ab-tile b { font:700 2.3rem/1 'Poppins',sans-serif; letter-spacing:-.04em; }
    .ab-tile.hero { color:#fff; background:linear-gradient(130deg,#6d4fd6,#b560d8 55%,#f06fb0); border:0; box-shadow:0 22px 46px rgba(124,92,224,.4); }
    .ab-tile.hero span { color:rgba(255,255,255,.8); } .ab-tile.hero b { font-size:3.2rem; }
    .ab-tile.hero::before, .ab-tile.hero::after { content:''; position:absolute; border-radius:50%; background:rgba(255,255,255,.14); }
    .ab-tile.hero::before { width:190px; height:190px; right:-50px; top:-70px; } .ab-tile.hero::after { width:110px; height:110px; right:60px; bottom:-60px; }
    .ab-tile.c b { color:#13915f; } .ab-tile.d b { color:#d9822b; }

    .ab-bar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; padding:8px; border-radius:999px; background:rgba(255,255,255,.82); border:1px solid rgba(255,255,255,.95); box-shadow:0 12px 32px rgba(124,92,224,.1); backdrop-filter:blur(12px); }
    .ab-search { position:relative; flex:1 1 240px; min-width:200px; }
    .ab-search svg { position:absolute; left:18px; top:50%; transform:translateY(-50%); width:18px; height:18px; fill:none; stroke:var(--acc); stroke-width:2; stroke-linecap:round; pointer-events:none; }
    .ab-search input { width:100%; height:46px; padding:0 18px 0 46px; border:0; outline:0; border-radius:999px; background:rgba(124,92,224,.07); font:500 .82rem 'Poppins',sans-serif; color:var(--ink); }
    .ab-search input:focus { background:#fff; box-shadow:0 0 0 3px rgba(124,92,224,.2); }
    .ab-seg { display:inline-flex; padding:4px; border-radius:999px; background:rgba(124,92,224,.08); }
    .ab-seg label { cursor:pointer; } .ab-seg input { position:absolute; opacity:0; pointer-events:none; }
    .ab-seg span { display:block; padding:.55rem 1rem; border-radius:999px; font:600 .72rem 'Poppins',sans-serif; color:var(--mut); transition:all .18s; }
    .ab-seg input:checked + span { background:#fff; color:var(--acc); box-shadow:0 4px 12px rgba(124,92,224,.2); }
    .ab-sel { height:46px; padding:0 38px 0 18px; border:0; outline:0; border-radius:999px; background-color:rgba(124,92,224,.08); font:600 .74rem 'Poppins',sans-serif; color:var(--ink); cursor:pointer; appearance:none; -webkit-appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%237c5ce0' stroke-width='2.6' stroke-linecap='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 15px center; }

    .ab-shelf { display:grid; grid-template-columns:repeat(auto-fill,minmax(186px,1fr)); gap:30px 22px; }
    .ab-bk { display:flex; flex-direction:column; gap:12px; animation:abRise .5s ease both; }
    .ab-poster { position:relative; aspect-ratio:2/3; border-radius:22px 22px 22px 8px; overflow:hidden; box-shadow:0 16px 30px rgba(60,40,140,.22); transition:transform .3s cubic-bezier(.2,.8,.2,1), box-shadow .3s; }
    .ab-bk:hover .ab-poster { transform:translateY(-8px) rotate(-1.2deg); box-shadow:0 28px 46px rgba(60,40,140,.32); }
    .ab-poster > a { position:absolute; inset:0; display:flex; align-items:center; justify-content:center; padding:16px; text-align:center; font:700 .9rem/1.3 'Playfair Display',serif; color:#fff; text-decoration:none; }
    .ab-poster img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
    .ab-pill { position:absolute; top:12px; left:12px; z-index:2; display:inline-flex; align-items:center; gap:.4rem; padding:.28rem .7rem; border-radius:99px; font:600 .62rem 'Poppins',sans-serif; background:rgba(255,255,255,.92); color:#13795a; backdrop-filter:blur(6px); }
    .ab-pill i { width:7px; height:7px; border-radius:50%; background:var(--mint); }
    .ab-pill.low { color:#9a6208; } .ab-pill.low i { background:var(--amber); }
    .ab-pill.out { color:#b02a47; } .ab-pill.out i { background:var(--bad); }
    .ab-over { position:absolute; left:10px; right:10px; bottom:10px; z-index:3; display:flex; gap:6px; padding:6px; border-radius:999px; background:rgba(255,255,255,.88); backdrop-filter:blur(10px); opacity:0; transform:translateY(10px); transition:all .22s ease; }
    .ab-over form { flex:1; margin:0; display:flex; } .ab-over .ab-btn { flex:1; height:34px; padding:0 .5rem; font-size:.7rem; }
    .ab-bk:hover .ab-over, .ab-bk:focus-within .ab-over { opacity:1; transform:none; }
    @media (hover:none) { .ab-over { opacity:1; transform:none; } }
    .ab-info h3 { margin:0; font:600 .88rem/1.35 'Poppins',sans-serif; letter-spacing:-.005em; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; }
    .ab-info p { margin:.25rem 0 .55rem; font-size:.72rem; color:var(--mut); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }

    .ab-empty { text-align:center; padding:60px 20px; border-radius:36px 36px 36px 10px; background:rgba(255,255,255,.7); border:1px dashed var(--line); }
    .ab-empty svg { width:104px; height:104px; margin-bottom:12px; }
    .ab-empty h3 { margin:0 0 .4rem; font:700 1.25rem 'Poppins',sans-serif; letter-spacing:-.02em; }
    .ab-empty p { margin:0 auto 1.4rem; max-width:380px; font-size:.8rem; line-height:1.65; color:var(--mut); }

    @media (max-width:1060px) {
        .ab-split { grid-template-columns:1fr; gap:26px; }
        .ab-side { position:static; flex-direction:row; align-items:center; text-align:left; padding:22px; border-radius:30px; }
        .ab-stage { width:120px; flex-shrink:0; } .ab-pv { text-align:left; } .ab-pv .ab-chips { justify-content:flex-start; }
        .ab-bento { grid-template-columns:1fr 1fr; } .ab-tile.hero { grid-column:1 / -1; }
    }
    @media (max-width:640px) {
        .ab-grid { grid-template-columns:1fr; }
        .ab-side { flex-direction:column; text-align:center; } .ab-pv { text-align:center; } .ab-pv .ab-chips { justify-content:center; }
        .ab-spot form { padding-left:16px; } .ab-spot svg.bc { display:none; } .ab-spot input { font-size:1rem; } .ab-spot .ab-btn { padding:0 1.1rem; }
        .ab-bar { border-radius:28px; } .ab-seg { width:100%; } .ab-seg label { flex:1; } .ab-seg span { text-align:center; } .ab-sel { flex:1; }
        .ab-dock { border-radius:28px; padding:14px; } .ab-dock .ab-acts { width:100%; } .ab-dock .ab-btn { flex:1; }
        .ab-shelf { grid-template-columns:repeat(2,minmax(0,1fr)); gap:24px 14px; }
        .ab-toast { left:14px; right:14px; max-width:none; }
    }
    @media (prefers-reduced-motion:reduce) { .ab *, .ab-toast { animation:none !important; transition:none !important; } }
</style>
@foreach(['ab_ok' => 'ok', 'ab_err' => 'err', 'ab_warn' => 'warn'] as $k => $t)
    @if(session($k))<div class="ab-toast {{ $t }}" role="status">{{ session($k) }}</div>@endif
@endforeach
<script>
    setTimeout(function () {
        document.querySelectorAll('.ab-toast').forEach(function (t) {
            t.style.transition = 'opacity .4s ease'; t.style.opacity = '0';
            setTimeout(function () { t.remove(); }, 450);
        });
    }, 6500);
</script>
@include('admin.partials.ui-magic')
@include('admin.partials.ui-dream')
@include('admin.partials.ui-gloss')
