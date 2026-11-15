@php
    $L = \App\Models\Loan::class;
    $tot = (int) $counts->sum();
    $bor = (int) ($counts['Borrowed'] ?? 0);
    $ret = (int) ($counts['Returned'] ?? 0);
    $pct = fn ($a, $b) => $b > 0 ? (int) round($a / $b * 100) : 0;
    $paidFine = max(0, $totalFine - $unpaidFine);
    $maxM = max(1, collect($months)->max('total'));
    $maxF = max(1, collect($months)->max('fine'));
    $busy = collect($months)->sortByDesc('total')->first();
    $nM = max(1, count($months));
    $avg = round($tot / $nM, 1);
    $short = function ($n) {
        if ($n >= 1000000) { return 'Rp ' . str_replace('.', ',', (string) round($n / 1000000, 1)) . 'jt'; }
        if ($n >= 1000) { return 'Rp ' . round($n / 1000) . 'rb'; }
        return 'Rp ' . $n;
    };
    $maxTop = max(1, collect($top)->max('n'));
    $niceI = fn ($v) => max(4, (int) (ceil($v / 4) * 4));
    $niceR = function ($v) {
        if ($v <= 0) { return 4000; }
        $st = $v / 4; $mg = pow(10, floor(log10($st))); return (int) (4 * ceil($st / $mg) * $mg);
    };
    $maxI = $niceI(max(1, collect($months)->max('total')));
    $maxR = $niceR(collect($months)->max('fine'));
    $bt = fn ($v, $mx) => round((300 - (285 - ($v / $mx) * 260)) / 300 * 100, 2);
    $curve = function ($vals, $mx) {
        $n = count($vals); if ($n === 0) { return ['', '']; }
        $pts = [];
        foreach ($vals as $i => $v) { $pts[] = [round(($i + .5) / $n * 1000, 1), round(285 - ($v / $mx) * 260, 1)]; }
        $d = 'M' . $pts[0][0] . ' ' . $pts[0][1];
        for ($i = 1; $i < $n; $i++) {
            $a = $pts[$i - 1]; $b = $pts[$i]; $m = round(($a[0] + $b[0]) / 2, 1);
            $d .= ' C' . $m . ' ' . $a[1] . ' ' . $m . ' ' . $b[1] . ' ' . $b[0] . ' ' . $b[1];
        }
        return [$d, $d . ' L' . $pts[$n - 1][0] . ' 285 L' . $pts[0][0] . ' 285 Z'];
    };
    $ppF = $totalFine > 0 ? round($paidFine / $totalFine * 100, 2) : 0;
    $puF = $totalFine > 0 ? round(100 - $ppF, 2) : 0;
    $gapF = ($ppF > 0 && $puF > 0) ? 6.5 : 0;
    $dPaid = $gapF ? max(0.01, $ppF - $gapF) : $ppF;
    $dUnp = $gapF ? max(0.01, $puF - $gapF) : $puF;
    $oUnp = $gapF ? $ppF + 2 : $ppF;
    $oPaid = $gapF ? -2 : 0;
    [$dB, $aB] = $curve(array_column($months, 'borrowed'), $maxI);
    [$dR, $aR] = $curve(array_column($months, 'returned'), $maxI);
    $paidArr = []; $unpArr = [];
    foreach ($months as $mm) { $paidArr[] = max(0, $mm['fine'] - $mm['unpaid']); $unpArr[] = $mm['unpaid']; }
    [$dP, $aP] = $curve($paidArr, $maxR);
    [$dU, $aU] = $curve($unpArr, $maxR);
@endphp
<x-admin-layout title="Laporan Peminjaman">
<style>
    @keyframes zrWipe { from { clip-path:inset(0 100% 0 0); } to { clip-path:inset(0 0 0 0); } }
    @keyframes zrRise { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:none; } }
    @keyframes zrPop { from { opacity:0; transform:translate(-50%,50%) scale(0); } to { opacity:1; transform:translate(-50%,50%) scale(1); } }
    @keyframes zrTw { 0%,100% { opacity:0; transform:scale(.3) rotate(0); } 50% { opacity:1; transform:scale(1) rotate(45deg); } }
    @keyframes zrFlow { from { background-position:0 0; } to { background-position:200% 0; } }
    @keyframes zrRing { 0%,100% { transform:translate(-50%,50%) scale(1); opacity:.5; } 50% { transform:translate(-50%,50%) scale(1.9); opacity:0; } }
    @keyframes zrFloat { 0%,100% { transform:translateY(0); } 50% { transform:translateY(-8px); } }

    .zr { display:grid !important; gap:20px; font-family:'Poppins',sans-serif; }
    .zr > * { min-width:0; animation:zrRise .6s cubic-bezier(.2,.8,.2,1) backwards; }
    .zr > *:nth-child(2) { animation-delay:.05s; } .zr > *:nth-child(3) { animation-delay:.1s; } .zr > *:nth-child(4) { animation-delay:.15s; } .zr > *:nth-child(5) { animation-delay:.2s; } .zr > *:nth-child(6) { animation-delay:.25s; }

    .zr-hero { position:relative; overflow:hidden; border-radius:46px 18px 46px 18px; padding:28px 32px; color:#fff;
        background:linear-gradient(120deg,#7b5cf0 0%,#b575f0 45%,#f283c0 100%); box-shadow:0 24px 50px -22px rgba(123,92,240,.75), inset 0 1px 0 rgba(255,255,255,.5);
        display:flex; flex-wrap:wrap; gap:18px; align-items:center; justify-content:space-between; }
    .zr-hero::before { content:''; position:absolute; width:340px; height:340px; right:-60px; top:-150px; border-radius:50%; background:radial-gradient(circle,rgba(255,255,255,.4),transparent 65%); }
    .zr-hero h2 { margin:0; font-family:'Playfair Display',Georgia,serif; font-size:1.9rem; line-height:1.15; position:relative; z-index:1; }
    .zr-hero p { margin:6px 0 0; font-size:.86rem; opacity:.92; max-width:520px; position:relative; z-index:1; }
    .zr-per { display:inline-flex; align-items:center; gap:8px; margin-top:12px; padding:6px 14px; border-radius:99px; background:rgba(255,255,255,.22); font-size:.74rem; font-weight:600; position:relative; z-index:1; box-shadow:inset 0 1px 0 rgba(255,255,255,.6); }
    .zr-dl { position:relative; z-index:1; display:inline-flex; align-items:center; gap:10px; padding:14px 26px; border-radius:99px; background:#fff; color:#6a3fd0 !important; font-weight:700; font-size:.88rem; text-decoration:none !important; box-shadow:0 14px 30px -10px rgba(60,20,120,.55); transition:transform .25s; }
    .zr-dl:hover { transform:translateY(-3px) scale(1.03); }
    .zr-dl svg { width:18px; height:18px; }
    .zr-star { position:absolute; width:14px; height:14px; clip-path:polygon(50% 0,61% 39%,100% 50%,61% 61%,50% 100%,39% 61%,0 50%,39% 39%); background:#fff; animation:zrTw 4s ease-in-out infinite; z-index:1; }

    .zr .zr-filter { display:flex !important; flex-wrap:wrap; gap:12px 18px; align-items:center; height:auto !important; min-height:0 !important; max-height:none !important; overflow:visible !important; position:relative !important;
        border-radius:30px 14px 30px 14px; padding:14px 20px; background:linear-gradient(160deg,rgba(255,255,255,.92),rgba(247,240,255,.7)); box-shadow:inset 0 1px 0 #fff, 0 14px 34px -20px rgba(120,90,200,.5); }
    .zr-filter .lbl { font-size:.72rem; font-weight:600; letter-spacing:.1em; text-transform:uppercase; color:#6b6790; }
    .zr-pills { display:flex; flex-wrap:wrap; gap:8px; }
    .zr-pill { padding:8px 16px; border-radius:99px; font-size:.78rem; font-weight:500; color:#5a4fb0 !important; text-decoration:none !important; background:rgba(139,92,246,.1); transition:all .25s; }
    .zr-pill:hover { background:rgba(139,92,246,.2); transform:translateY(-1px); }
    .zr-pill.on { color:#fff !important; background:linear-gradient(135deg,#8b5cf6,#d46bd0); box-shadow:0 8px 18px -6px rgba(139,92,246,.7), inset 0 1px 0 rgba(255,255,255,.5); }
    .zr-adv { margin-left:auto; }
    .zr-adv summary { cursor:pointer; list-style:none; font-size:.76rem; font-weight:500; color:#7a5fe0; padding:8px 14px; border-radius:99px; background:rgba(255,255,255,.8); }
    .zr-adv summary::-webkit-details-marker { display:none; }
    .zr-adv[open] { flex-basis:100%; margin-left:0; }
    .zr-adv form { display:flex; flex-wrap:wrap; gap:14px; align-items:flex-end; margin-top:12px; padding-top:14px; border-top:1px dashed rgba(139,92,246,.25); }
    .zr-adv label { display:block; font-size:.7rem; color:#6b6790; margin-bottom:4px; font-weight:500; }
    .zr-help { font-size:.7rem; color:#8b87a8; flex-basis:100%; margin:-4px 0 0; }

    .zr-kpis { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; }
    .zr-k { position:relative; overflow:hidden; padding:20px 20px 18px; border-radius:36px 14px 36px 14px; display:flex; align-items:center; justify-content:space-between; gap:12px; min-width:0;
        background:radial-gradient(110% 90% at 100% 0%, var(--gl), transparent 62%), linear-gradient(160deg,rgba(255,255,255,.95),rgba(250,244,255,.7));
        box-shadow:0 18px 40px -20px var(--sh), inset 0 1px 0 #fff; }
    .zr-k:nth-child(even) { border-radius:14px 36px 14px 36px; }
    .zr-k .t { font-size:.66rem; letter-spacing:.12em; text-transform:uppercase; font-weight:600; color:#6b6790; }
    .zr-k .n { font-family:'Poppins',sans-serif; font-size:2.2rem; line-height:1.1; margin:6px 0 2px; font-weight:700; letter-spacing:-.02em; background:linear-gradient(120deg,var(--d1),var(--d2)); -webkit-background-clip:text; background-clip:text; color:transparent; }
    .zr-k .n.sm { font-size:1.3rem; white-space:nowrap; }
    .zr-k .s { font-size:.7rem; color:#8b87a8; line-height:1.4; }
    .zr-ringw { position:relative; width:76px; height:76px; flex:0 0 auto; display:grid; place-items:center; }
    .zr-ringw { position:relative; flex:0 0 auto; display:grid; place-items:center; }

    .zr-sec { position:relative; overflow:hidden; border-radius:44px 16px 44px 16px; padding:24px 26px 24px; background:linear-gradient(160deg,rgba(255,255,255,.94),rgba(249,243,255,.68)); box-shadow:0 22px 50px -26px rgba(120,90,200,.55), inset 0 1px 0 #fff; }
    .zr-sec::after { content:''; position:absolute; right:-70px; top:-70px; width:220px; height:220px; border-radius:50%; background:radial-gradient(circle,var(--gl,rgba(167,139,250,.28)),transparent 70%); pointer-events:none; }
    .zr-sh { position:relative; z-index:1; display:flex; flex-wrap:wrap; align-items:center; gap:10px 14px; margin-bottom:6px; }
    .zr-tag { padding:5px 13px; border-radius:99px; font-size:.62rem; letter-spacing:.16em; font-weight:700; color:#fff; background:linear-gradient(135deg,var(--a1),var(--a2)); box-shadow:0 6px 14px -5px var(--sh); }
    .zr-sh h3 { margin:0; font-family:'Playfair Display',Georgia,serif; font-size:1.35rem; color:#3b2f86; }
    .zr-sd { position:relative; z-index:1; margin:0 0 14px; font-size:.8rem; color:#7d79a0; }
    .zr-legend { display:flex; flex-wrap:wrap; gap:14px; margin-left:auto; font-size:.72rem; color:#5b577d; }
    .zr-legend i { display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:6px; vertical-align:-1px; background:var(--c); box-shadow:0 0 8px var(--c); }

    .zr-chips { position:relative; z-index:1; display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-bottom:18px; }
    .zr-chip { padding:12px 16px; border-radius:24px 10px 24px 10px; background:linear-gradient(150deg,rgba(255,255,255,.92),rgba(243,235,255,.6)); box-shadow:inset 0 1px 0 #fff, 0 10px 24px -16px rgba(120,90,200,.5); min-width:0; }
    .zr-chip:nth-child(even) { border-radius:10px 24px 10px 24px; }
    .zr-chip .t { font-size:.6rem; letter-spacing:.13em; text-transform:uppercase; color:#8b87a8; font-weight:600; }
    .zr-chip .v { font-size:1.2rem; color:#3b2f86; line-height:1.25; margin-top:2px; font-weight:700; letter-spacing:-.01em; }
    .zr-chip .d { font-size:.68rem; color:#7d79a0; }

    .zr-chart { position:relative; z-index:1; padding-bottom:46px; }
    .zr-body { position:relative; height:290px; margin-left:46px; border-radius:6px; }
    .zr-grid { position:absolute; left:0; right:0; height:0; border-top:1px dashed rgba(130,110,200,.22); }
    .zr-base { border-top:2px solid rgba(130,110,200,.28); border-top-style:solid; }
    .zr-yl { position:absolute; left:-46px; width:40px; text-align:right; transform:translateY(50%); font-size:.62rem; color:#9893b8; font-weight:500; white-space:nowrap; }
    .zr-area { position:absolute; inset:0; width:100%; height:100%; overflow:visible; animation:zrWipe 1.6s cubic-bezier(.2,.8,.2,1) .25s backwards; }
    .zr-area path { vector-effect:non-scaling-stroke; }
    .zr-pt { position:absolute; width:15px; height:15px; border-radius:50%; background:#fff; box-shadow:0 0 0 3.5px var(--c), 0 0 16px 3px var(--c); transform:translate(-50%,50%); animation:zrPop .5s cubic-bezier(.3,1.6,.5,1) calc(1s + var(--i) * 45ms) backwards; z-index:2; }
    .zr-pt::after { content:''; position:absolute; inset:0; border-radius:50%; border:2px solid var(--c); animation:zrRing 2.8s ease-out infinite; }
    .zr-hit { position:absolute; top:0; bottom:0; z-index:3; }
    .zr-hit::before { content:''; position:absolute; left:50%; top:0; bottom:0; width:2px; margin-left:-1px; background:linear-gradient(rgba(139,92,246,0),rgba(139,92,246,.45)); opacity:0; transition:opacity .2s; }
    .zr-hit:hover::before { opacity:1; }
    .zr-tip { position:absolute; left:50%; top:8px; transform:translate(-50%,6px); padding:9px 13px; border-radius:16px; background:rgba(46,37,99,.94); color:#fff; font-size:.68rem; line-height:1.6; white-space:nowrap; opacity:0; pointer-events:none; transition:all .2s; box-shadow:0 14px 28px -8px rgba(30,20,80,.65); }
    .zr-hit:hover .zr-tip { opacity:1; transform:translate(-50%,0); }
    .zr-hit:first-of-type .zr-tip { left:0; transform:translate(0,6px); } .zr-hit:first-of-type:hover .zr-tip { transform:translate(0,0); }
    .zr-xl { position:absolute; left:46px; right:0; bottom:0; display:flex; }
    .zr-xl span { flex:1; text-align:center; font-size:.68rem; color:#6b6790; font-weight:500; line-height:1.25; min-width:0; }
    .zr-xl small { display:block; font-size:.58rem; color:#a09bbd; }

    .zr-fav { display:grid !important; gap:14px; position:relative; z-index:1; }
    .zr-fv { display:grid !important; grid-template-columns:36px minmax(0,1fr) auto; gap:14px; align-items:center; }
    .zr-medal { width:36px; height:36px; border-radius:50%; display:grid; place-items:center; font-weight:700; font-size:.84rem; color:#fff; background:linear-gradient(135deg,#a78bfa,#f0abfc); box-shadow:0 6px 12px -4px rgba(139,92,246,.6), inset 0 2px 2px rgba(255,255,255,.6); }
    .zr-fv:first-child .zr-medal { background:linear-gradient(135deg,#f6c453,#f58fb0); }
    .zr-name { font-size:.86rem; color:#3a3560; font-weight:500; margin-bottom:6px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .zr-meter { display:block !important; width:100% !important; height:10px; border-radius:99px; background:rgba(130,110,200,.14); overflow:hidden; box-shadow:inset 0 1px 2px rgba(80,60,140,.12); }
    .zr-meter i { display:block; height:100%; border-radius:99px; width:calc(var(--w) * 1%); background:linear-gradient(90deg,#8b5cf6,#e879b9,#8b5cf6); background-size:200% 100%; animation:zrFlow 4s linear infinite; }
    .zr-cnt { font-weight:700; font-size:.92rem; color:#4b3f99; white-space:nowrap; }
    .zr-cnt small { font-weight:500; font-size:.66rem; color:#8b87a8; }


    @keyframes zrDraw { from { stroke-dasharray:0 100; } }
    @keyframes zrSpin { to { transform:rotate(360deg); } }
    @keyframes zrGlow { 0%,100% { filter:drop-shadow(0 0 3px var(--a1)); } 50% { filter:drop-shadow(0 0 9px var(--a1)); } }

    /* donat */
    .zr-dn, .zr-share svg, .zr-bigdn > svg { transform:rotate(-90deg); overflow:visible; display:block; }
    .zr-dn .trk, .zr-share .trk, .zr-bigdn .trk { fill:none; stroke:rgba(130,110,200,.13); stroke-width:3.4; }
    .zr-dn .arc, .zr-share .arc { fill:none; stroke-width:3.4; stroke-linecap:round; stroke-dasharray:var(--p) 100; animation:zrDraw 1.5s cubic-bezier(.2,.8,.2,1) .3s backwards, zrGlow 4s ease-in-out 2s infinite; }
    .zr-bigdn .arc { fill:none; stroke-width:3.4; stroke-linecap:round; animation:zrDraw 1.6s cubic-bezier(.2,.8,.2,1) .3s backwards, zrGlow 4s ease-in-out 2s infinite; }
    .zr-bigdn .arc.full { stroke-dasharray:100 100; stroke-linecap:butt; }
    .zr-bigdn .orb { fill:none; stroke:var(--a1); stroke-width:.22; stroke-dasharray:.7 1.5; opacity:.7; transform-origin:18px 18px; animation:zrSpin 40s linear infinite; }
    .zr-ringw { width:86px; height:86px; }
    .zr-ringw::before { content:''; position:absolute; inset:13px; border-radius:50%; background:radial-gradient(circle at 30% 22%,#fff,rgba(255,255,255,.7) 55%,rgba(232,222,255,.8)); box-shadow:inset 0 -5px 9px rgba(130,100,220,.16), 0 8px 16px -6px var(--sh); }
    .zr-ringw .zr-dn { position:absolute; inset:0; width:100%; height:100%; }
    .zr-dc { position:relative; z-index:1; font-weight:700; font-size:1rem; color:var(--d1); line-height:1; display:flex; align-items:baseline; justify-content:center; }
    .zr-dc em { font-style:normal; font-size:.6rem; font-weight:600; opacity:.7; margin-left:1px; }
    .zr-dc svg { width:22px; height:22px; }

    .zr-bigdn { position:relative; width:206px; height:206px; flex:0 0 auto; }
    .zr-bigdn > svg { width:100%; height:100%; filter:drop-shadow(0 14px 18px var(--sh)); }
    .zr-core { position:absolute; inset:34px; border-radius:50%; display:grid; place-content:center; text-align:center;
        background:radial-gradient(circle at 30% 22%,#fff,rgba(255,255,255,.78) 55%,rgba(235,226,255,.85)); box-shadow:inset 0 -8px 16px rgba(130,100,220,.16), 0 10px 24px -8px var(--sh), inset 0 2px 3px #fff; }
    .zr-core svg { width:44px; height:44px; padding:10px; border-radius:50%; background:linear-gradient(145deg,#22c597,#7ee7d0); box-shadow:0 12px 22px -8px rgba(34,197,151,.7), inset 0 2px 3px rgba(255,255,255,.7); margin:auto; }
    .zr-core.flat small { font-size:.58rem; letter-spacing:.14em; text-transform:uppercase; color:#8b87a8; font-weight:600; }
    .zr-core.flat b { font-size:1.45rem; font-weight:700; letter-spacing:-.02em; background:linear-gradient(120deg,#3b2f86,#c0408f); -webkit-background-clip:text; background-clip:text; color:transparent; }
    .zr-spin { position:absolute; inset:-10px; animation:zrSpin 14s linear infinite; pointer-events:none; }
    .zr-spin i { position:absolute; left:50%; top:0; width:12px; height:12px; margin-left:-6px; transform-origin:6px 113px; transform:rotate(var(--a)); background:linear-gradient(135deg,#fff,#7ee7d0); clip-path:polygon(50% 0,61% 39%,100% 50%,61% 61%,50% 100%,39% 61%,0 50%,39% 39%); filter:drop-shadow(0 0 4px #22c597); }

    .zr-clean { position:relative; z-index:1; display:flex; flex-wrap:wrap; gap:26px 40px; align-items:center; justify-content:center; padding:10px 4px 6px; }
    .zr-cleant { flex:1 1 320px; max-width:520px; }
    .zr-cleant h4 { margin:0 0 6px; font-family:'Playfair Display',Georgia,serif; font-size:1.7rem; color:#0f7a63; }
    .zr-cleant p { margin:0 0 16px; font-size:.86rem; color:#6b6790; line-height:1.6; }
    .zr-mini3 { display:grid; grid-template-columns:repeat(3,1fr); gap:10px; }
    .zr-mini3 > div { padding:12px 14px; border-radius:20px 8px 20px 8px; background:linear-gradient(150deg,rgba(255,255,255,.95),rgba(225,250,242,.7)); box-shadow:inset 0 1px 0 #fff, 0 10px 22px -14px rgba(34,197,151,.6); }
    .zr-mini3 > div:nth-child(even) { border-radius:8px 20px 8px 20px; }
    .zr-mini3 small { display:block; font-size:.58rem; letter-spacing:.12em; text-transform:uppercase; color:#8b87a8; font-weight:600; }
    .zr-mini3 b { font-size:1rem; color:#0f7a63; font-weight:700; }

    .zr-finegrid { position:relative; z-index:1; display:grid; grid-template-columns:300px minmax(0,1fr); gap:26px; align-items:start; }
    .zr-dcard { display:flex; flex-direction:column; align-items:center; gap:18px; padding:22px 16px 18px; border-radius:36px 14px 36px 14px; background:linear-gradient(160deg,rgba(255,255,255,.95),rgba(255,240,248,.7)); box-shadow:0 18px 38px -22px var(--sh), inset 0 1px 0 #fff; }
    .zr-dl2 { list-style:none; margin:0; padding:0; display:grid; gap:9px; width:100%; }
    .zr-dl2 li { display:flex; align-items:center; gap:9px; padding:10px 12px; border-radius:16px; background:rgba(255,255,255,.75); font-size:.76rem; color:#4b476b; box-shadow:inset 0 1px 0 #fff; }
    .zr-dl2 li i { width:11px; height:11px; border-radius:50%; background:var(--c); box-shadow:0 0 0 3px rgba(255,255,255,.9), 0 0 10px var(--c); flex:0 0 auto; }
    .zr-dl2 li span { margin-left:auto; font-weight:700; color:#3b2f86; text-align:right; }
    .zr-dl2 li em { display:block; font-style:normal; font-size:.62rem; color:#8b87a8; font-weight:500; }

    /* buku favorit */
    .zr-podium { position:relative; z-index:1; display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
    .zr-bk { position:relative; display:grid; grid-template-columns:64px minmax(0,1fr) auto; gap:16px; align-items:center; padding:16px 18px; border-radius:30px 12px 30px 12px;
        background:linear-gradient(155deg,rgba(255,255,255,.95),rgba(238,244,255,.7)); box-shadow:0 16px 34px -22px rgba(56,100,200,.55), inset 0 1px 0 #fff; animation:zrRise .6s cubic-bezier(.2,.8,.2,1) calc(var(--i) * 70ms + .1s) backwards; transition:transform .3s, box-shadow .3s; }
    .zr-bk:nth-child(even) { border-radius:12px 30px 12px 30px; }
    .zr-bk:hover { transform:translateY(-4px); box-shadow:0 24px 40px -22px rgba(56,100,200,.7), inset 0 1px 0 #fff; }
    .zr-bk.first { grid-column:1 / -1; grid-template-columns:112px minmax(0,1fr) auto; gap:26px; padding:24px 28px; border-radius:44px 16px 44px 16px;
        background:radial-gradient(90% 120% at 0% 0%,rgba(255,226,170,.5),transparent 55%), linear-gradient(155deg,rgba(255,255,255,.96),rgba(250,240,255,.75)); box-shadow:0 26px 50px -26px rgba(190,120,200,.7), inset 0 1px 0 #fff; }
    .zr-rank { position:absolute; left:18px; top:-10px; z-index:3; padding:4px 11px; border-radius:99px; font-size:.62rem; font-weight:700; letter-spacing:.06em; color:#fff; background:linear-gradient(135deg,#a78bfa,#7c8cff); box-shadow:0 6px 12px -4px rgba(124,140,255,.7); }
    .zr-bk.r1 .zr-rank { background:linear-gradient(135deg,#f6b73c,#f58fb0); box-shadow:0 8px 16px -4px rgba(246,160,100,.75); font-size:.7rem; padding:5px 14px; }
    .zr-bk.r1 .zr-rank::before { content:'\2605\00a0'; }
    .zr-bk.r2 .zr-rank { background:linear-gradient(135deg,#9db4d8,#c9d6ee); color:#3a4a6b; }
    .zr-bk.r3 .zr-rank { background:linear-gradient(135deg,#e3a27a,#f2c3a3); color:#6b3b1f; }
    .zr-cv { position:relative; width:64px; height:92px; border-radius:4px 10px 10px 4px; overflow:hidden; background:linear-gradient(145deg,#c9b8ff,#ffc8e6); display:grid; place-items:center; color:#fff; font-family:'Playfair Display',serif; font-size:1.8rem; font-weight:700;
        box-shadow:-3px 4px 0 -1px rgba(120,90,200,.25), 0 14px 22px -8px rgba(80,60,160,.55), inset 3px 0 5px rgba(0,0,0,.14); }
    .zr-bk.first .zr-cv { width:112px; height:160px; font-size:3rem; border-radius:5px 14px 14px 5px; box-shadow:-5px 6px 0 -2px rgba(120,90,200,.25), 0 24px 36px -12px rgba(120,60,160,.6), inset 4px 0 8px rgba(0,0,0,.16); transform:rotate(-3deg); margin-top:10px; }
    .zr-cv img { position:absolute; inset:0; width:100%; height:100%; object-fit:cover; }
    .zr-cv::after { content:''; position:absolute; inset:0; background:linear-gradient(115deg,rgba(255,255,255,.5) 0%,rgba(255,255,255,0) 40%); pointer-events:none; }
    .zr-bi { min-width:0; }
    .zr-bt { font-weight:600; font-size:.92rem; color:#2f2a5c; line-height:1.3; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .zr-bk.first .zr-bt { font-family:'Playfair Display',Georgia,serif; font-size:1.65rem; line-height:1.2; }
    .zr-ba { font-size:.74rem; color:#8b87a8; margin-top:3px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .zr-bn { margin-top:9px; font-size:.74rem; color:#6b6790; }
    .zr-bn b { font-size:1.25rem; color:#4b3f99; font-weight:700; margin-right:2px; }
    .zr-bk.first .zr-bn b { font-size:2rem; background:linear-gradient(120deg,#7b5cf0,#f283c0); -webkit-background-clip:text; background-clip:text; color:transparent; }
    .zr-share { position:relative; width:58px; height:58px; }
    .zr-bk.first .zr-share { width:92px; height:92px; }
    .zr-share svg { width:100%; height:100%; }
    .zr-share b { position:absolute; inset:0; display:grid; place-items:center; font-size:.76rem; font-weight:700; color:#4b3f99; }
    .zr-bk.first .zr-share b { font-size:1.1rem; }

    /* data lengkap */
    .zr-search { position:relative; z-index:1; display:flex; align-items:center; gap:10px; padding:4px 8px 4px 18px; margin-bottom:14px; border-radius:99px; background:rgba(255,255,255,.85); box-shadow:inset 0 2px 4px rgba(120,90,200,.1), 0 8px 20px -12px rgba(120,90,200,.45); color:#8b6fe0; transition:box-shadow .25s; }
    .zr-search:focus-within { box-shadow:0 0 0 3px rgba(167,139,250,.35), 0 10px 24px -10px rgba(139,92,246,.55); }
    .zr-search svg { width:18px; height:18px; flex:0 0 auto; }
    .zr-search input { all:unset; flex:1; padding:11px 4px; font-size:.84rem; color:#3a3560; font-family:inherit; min-width:0; }
    .zr-search input::placeholder { color:#a8a3c6; }
    .zr-search span { padding:7px 14px; border-radius:99px; font-size:.7rem; font-weight:600; color:#6a4fd0; background:rgba(139,92,246,.12); white-space:nowrap; }
    .zr-head, .zr-row { display:grid; grid-template-columns:minmax(0,1.15fr) minmax(0,1.2fr) minmax(0,2fr) minmax(130px,auto); gap:18px; align-items:center; }
    .zr-head { position:relative; z-index:1; padding:0 22px 8px; font-size:.62rem; letter-spacing:.14em; text-transform:uppercase; color:#8b87a8; font-weight:600; }
    .zr-rows { position:relative; z-index:1; display:grid; gap:10px; }
    .zr-row { position:relative; padding:14px 20px 14px 24px; border-radius:24px 10px 24px 10px; background:linear-gradient(160deg,rgba(255,255,255,.92),rgba(250,246,255,.72)); box-shadow:0 10px 24px -18px rgba(120,90,200,.55), inset 0 1px 0 #fff; transition:transform .25s, box-shadow .25s; animation:zrRise .5s ease backwards; }
    .zr-row:hover { transform:translateX(4px); box-shadow:0 16px 30px -16px rgba(120,90,200,.6), inset 0 1px 0 #fff; }
    .zr-row::before { content:''; position:absolute; left:9px; top:14px; bottom:14px; width:4px; border-radius:99px; background:linear-gradient(#a78bfa,#f0abfc); }
    .zr-row.s-borrowed::before { background:linear-gradient(#38a8f8,#7c8cff); box-shadow:0 0 10px rgba(56,168,248,.6); }
    .zr-row.s-returned::before { background:linear-gradient(#22c597,#7ee7d0); box-shadow:0 0 10px rgba(34,197,151,.55); }
    .zr-row.has-fine::before { background:linear-gradient(#fb6f92,#ffb3cc); box-shadow:0 0 10px rgba(251,111,146,.6); }
    .zr-who { display:flex; align-items:center; gap:12px; min-width:0; }
    .zr-av { width:42px; height:42px; border-radius:50%; flex:0 0 auto; display:grid; place-items:center; font-weight:700; color:#fff; background:linear-gradient(135deg,#8b5cf6,#f283c0); box-shadow:0 8px 16px -6px rgba(139,92,246,.7), inset 0 2px 3px rgba(255,255,255,.6); }
    .zr-who b, .zr-bk2 b { display:block; font-size:.86rem; color:#2f2a5c; font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .zr-who small, .zr-bk2 small { display:block; font-size:.68rem; color:#8b87a8; margin-top:2px; }
    .zr-who > div, .zr-bk2 { min-width:0; }
    .zr-tl { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; font-size:.78rem; color:#3a3560; font-weight:500; position:relative; }
    .zr-tl > div { padding:8px 10px; border-radius:14px; background:rgba(139,92,246,.07); }
    .zr-tl small { display:block; font-size:.58rem; letter-spacing:.1em; text-transform:uppercase; color:#9b96b8; font-weight:600; margin-bottom:2px; }
    .zr-state { display:flex; flex-direction:column; align-items:flex-start; gap:6px; }
    .zr-fn { font-size:.8rem; font-weight:700; color:#c2415d; }
    .zr-fn em { display:block; font-style:normal; font-size:.62rem; font-weight:500; color:#a2788a; }
    .zr-none { padding:30px; text-align:center; color:#8b87a8; font-size:.85rem; }
    .zr-pg { position:relative; z-index:1; margin-top:16px; }
    .zr-pg svg { width:18px; height:18px; }
    @media (max-width:1100px) { .zr-finegrid { grid-template-columns:1fr; } .zr-dcard { flex-direction:row; flex-wrap:wrap; justify-content:center; } .zr-dl2 { max-width:340px; } .zr-head { display:none; } .zr-head, .zr-row { grid-template-columns:1fr 1fr; } .zr-tl, .zr-state { grid-column:1 / -1; } .zr-state { flex-direction:row; align-items:center; gap:14px; } }
    @media (max-width:760px) { .zr-podium { grid-template-columns:1fr; } .zr-bk.first { grid-template-columns:84px minmax(0,1fr); gap:18px; } .zr-bk.first .zr-cv { width:84px; height:120px; } .zr-bk.first .zr-share { display:none; } .zr-mini3 { grid-template-columns:1fr; } .zr-bigdn { width:176px; height:176px; } .zr-core { inset:28px; } .zr-row { grid-template-columns:1fr; } .zr-tl { grid-template-columns:1fr; } }

    .zr-empty { position:relative; z-index:1; display:flex; flex-direction:column; align-items:center; gap:10px; padding:26px 10px 14px; text-align:center; color:#7d79a0; font-size:.84rem; }
    .zr-empty .gem { width:64px; height:64px; display:grid; place-items:center; border-radius:50%; background:linear-gradient(145deg,#d8c8ff,#ffd1ea); box-shadow:0 16px 30px -10px rgba(167,139,250,.7), inset 0 2px 4px rgba(255,255,255,.9); animation:zrFloat 4s ease-in-out infinite; }
    .zr-empty .gem svg { width:28px; height:28px; }
    .zr-empty b { color:#3b2f86; font-size:1rem; }
    .zr-tbl { position:relative; z-index:1; overflow-x:auto; }
    @media (max-width:1100px) { .zr-kpis { grid-template-columns:repeat(2,1fr); } }
    @media (max-width:700px) { .zr-chips { grid-template-columns:1fr; } .zr-kpis { grid-template-columns:1fr; } .zr-hero { padding:22px; } .zr-xl small { display:none; } .zr-body { height:230px; } }
    @media (prefers-reduced-motion:reduce) { .zr > *, .zr-area, .zr-pt, .zr-pt::after, .zr-star, .zr-meter i, .zr-empty .gem { animation:none !important; } }
</style>

<div class="zr">
    <div class="zr-hero">
        <i class="zr-star" style="left:46%;top:18px"></i><i class="zr-star" style="left:62%;bottom:20px;width:9px;height:9px;animation-delay:1.3s"></i><i class="zr-star" style="right:36%;top:46px;width:10px;height:10px;animation-delay:2.2s"></i>
        <div>
            <h2>Laporan Peminjaman Buku</h2>
            <p>Pantau berapa buku dipinjam, dikembalikan, dan denda yang terkumpul di perpustakaan, bulan demi bulan.</p>
            <span class="zr-per">&#10022; Periode: {{ $periode }}</span>
        </div>
        <a class="zr-dl" href="{{ route('admin.reports.export', ['dari' => $dari, 'sampai' => $sampai, 'status' => $status]) }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12m0 0-4.5-4.5M12 15l4.5-4.5M4 19h16"/></svg>
            Unduh Laporan PDF
        </a>
    </div>

    <div class="zr-filter">
        <span class="lbl">Lihat periode</span>
        <div class="zr-pills">
            @foreach($presets as $p)
                <a class="zr-pill {{ $p['on'] ? 'on' : '' }}" href="{{ route('admin.reports.index', $p['q']) }}">{{ $p['label'] }}</a>
            @endforeach
        </div>
        <details class="zr-adv">
            <summary>&#9998; Atur tanggal sendiri</summary>
            <form method="GET" action="{{ route('admin.reports.index') }}">
                <div><label>Dari tanggal</label><input class="tl-input" type="date" name="dari" value="{{ $dari }}"></div>
                <div><label>Sampai tanggal</label><input class="tl-input" type="date" name="sampai" value="{{ $sampai }}"></div>
                <div>
                    <label>Hanya tampilkan status</label>
                    <select class="tl-input" name="status">
                        <option value="">Semua status</option>
                        @foreach($L::LABELS as $k => $v)<option value="{{ $k }}" @selected($status === $k)>{{ $v }}</option>@endforeach
                    </select>
                </div>
                <button class="tl-btn" type="submit">Terapkan</button>
                <p class="zr-help">Tanggal yang dipakai adalah tanggal siswa mengajukan peminjaman buku.</p>
            </form>
        </details>
    </div>

    <div class="zr-kpis">
        <div class="zr-k" style="--a1:#8b5cf6;--a2:#c084fc;--d1:#4c2fa8;--d2:#a03a9a;--gl:rgba(167,139,250,.32);--sh:rgba(139,92,246,.45)">
            <div><div class="t">Total Peminjaman</div><div class="n">{{ number_format($tot) }}</div><div class="s">buku diajukan pada periode ini</div></div>
            <div class="zr-ringw">
                <svg class="zr-dn" viewBox="0 0 36 36" style="--p:100"><defs><linearGradient id="zrk1" x1="0" y1="0" x2="1" y2="1"><stop offset="0" style="stop-color:var(--a1)"/><stop offset="1" style="stop-color:var(--a2)"/></linearGradient></defs><circle class="trk" cx="18" cy="18" r="15.9155"/><circle class="arc" cx="18" cy="18" r="15.9155" stroke="url(#zrk1)" style="{{ (100) > 0 ? '' : 'display:none' }}"/></svg>
                <div class="zr-dc"><svg viewBox="0 0 24 24" fill="none" stroke="#6d45d8" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 3 8l9 5 9-5-9-5Z"/><path d="m3 13 9 5 9-5"/></svg></div>
            </div>
        </div>
        <div class="zr-k" style="--a1:#38a8f8;--a2:#7c8cff;--d1:#1f5fb8;--d2:#5b3fc4;--gl:rgba(125,190,255,.34);--sh:rgba(56,168,248,.45)">
            <div><div class="t">Masih Dipinjam</div><div class="n">{{ number_format($bor) }}</div><div class="s">belum dikembalikan siswa</div></div>
            <div class="zr-ringw">
                <svg class="zr-dn" viewBox="0 0 36 36" style="--p:$pct($bor, $tot)"><defs><linearGradient id="zrk2" x1="0" y1="0" x2="1" y2="1"><stop offset="0" style="stop-color:var(--a1)"/><stop offset="1" style="stop-color:var(--a2)"/></linearGradient></defs><circle class="trk" cx="18" cy="18" r="15.9155"/><circle class="arc" cx="18" cy="18" r="15.9155" stroke="url(#zrk2)" style="{{ ($pct($bor, $tot)) > 0 ? '' : 'display:none' }}"/></svg>
                <div class="zr-dc">{{ $pct($bor, $tot) }}<em>%</em></div>
            </div>
        </div>
        <div class="zr-k" style="--a1:#22c597;--a2:#5ddcc0;--d1:#0f7a63;--d2:#1c8f9e;--gl:rgba(110,231,190,.36);--sh:rgba(34,197,151,.42)">
            <div><div class="t">Sudah Kembali</div><div class="n">{{ number_format($ret) }}</div><div class="s">peminjaman selesai</div></div>
            <div class="zr-ringw">
                <svg class="zr-dn" viewBox="0 0 36 36" style="--p:$pct($ret, $tot)"><defs><linearGradient id="zrk3" x1="0" y1="0" x2="1" y2="1"><stop offset="0" style="stop-color:var(--a1)"/><stop offset="1" style="stop-color:var(--a2)"/></linearGradient></defs><circle class="trk" cx="18" cy="18" r="15.9155"/><circle class="arc" cx="18" cy="18" r="15.9155" stroke="url(#zrk3)" style="{{ ($pct($ret, $tot)) > 0 ? '' : 'display:none' }}"/></svg>
                <div class="zr-dc">{{ $pct($ret, $tot) }}<em>%</em></div>
            </div>
        </div>
        <div class="zr-k" style="--a1:#fb6f92;--a2:#f7a1c4;--d1:#b3315a;--d2:#c0408f;--gl:rgba(255,160,190,.36);--sh:rgba(251,111,146,.42)">
            <div><div class="t">Total Denda</div><div class="n sm">{{ $L::rupiah($totalFine) }}</div><div class="s">belum dibayar {{ $L::rupiah($unpaidFine) }}</div></div>
            <div class="zr-ringw">
                <svg class="zr-dn" viewBox="0 0 36 36" style="--p:$totalFine > 0 ? $pct($paidFine, $totalFine) : 0"><defs><linearGradient id="zrk4" x1="0" y1="0" x2="1" y2="1"><stop offset="0" style="stop-color:var(--a1)"/><stop offset="1" style="stop-color:var(--a2)"/></linearGradient></defs><circle class="trk" cx="18" cy="18" r="15.9155"/><circle class="arc" cx="18" cy="18" r="15.9155" stroke="url(#zrk4)" style="{{ ($totalFine > 0 ? $pct($paidFine, $totalFine) : 0) > 0 ? '' : 'display:none' }}"/></svg>
                <div class="zr-dc">{{ $totalFine > 0 ? $pct($paidFine, $totalFine) : 0 }}<em>%</em></div>
            </div>
        </div>
    </div>

    <div class="zr-sec" style="--a1:#8b5cf6;--a2:#d46bd0;--sh:rgba(139,92,246,.5);--gl:rgba(167,139,250,.28)">
        <div class="zr-sh">
            <span class="zr-tag">LAPORAN 1</span><h3>Peminjaman per Bulan</h3>
            <div class="zr-legend"><span style="--c:#38a8f8"><i></i>Masih dipinjam</span><span style="--c:#22c597"><i></i>Sudah kembali</span></div>
        </div>
        <p class="zr-sd">Jumlah buku yang diajukan siswa di setiap bulan. Arahkan kursor ke grafik untuk melihat rinciannya.</p>
        @if($tot === 0)
            <div class="zr-empty"><div class="gem"><svg viewBox="0 0 24 24" fill="#fff" stroke="#fff" stroke-width="1" stroke-linejoin="round"><path d="M12 2.5 14.2 9.8 21.5 12l-7.3 2.2L12 21.5l-2.2-7.3L2.5 12l7.3-2.2L12 2.5Z"/></svg></div><b>Belum ada peminjaman</b>Coba pilih periode yang lebih panjang di atas.</div>
        @else
        <div class="zr-chips">
            <div class="zr-chip"><div class="t">Bulan tersibuk</div><div class="v">{{ $busy ? $busy['label'] . ' ' . $busy['year'] : '-' }}</div><div class="d">{{ $busy ? $busy['total'] : 0 }} peminjaman</div></div>
            <div class="zr-chip"><div class="t">Rata-rata per bulan</div><div class="v">{{ str_replace('.', ',', (string) $avg) }} buku</div><div class="d">dari {{ $nM }} bulan terpilih</div></div>
            <div class="zr-chip"><div class="t">Tingkat pengembalian</div><div class="v">{{ $pct($ret, $tot) }}%</div><div class="d">{{ $ret }} dari {{ $tot }} sudah kembali</div></div>
        </div>
        <div class="zr-chart">
            <div class="zr-body">
                @foreach([0, 1, 2, 3, 4] as $k)
                    <div class="zr-grid {{ $k === 0 ? 'zr-base' : '' }}" style="bottom:{{ $bt($maxI * $k / 4, $maxI) }}%"></div>
                    <span class="zr-yl" style="bottom:{{ $bt($maxI * $k / 4, $maxI) }}%">{{ $maxI * $k / 4 }}</span>
                @endforeach
                <svg class="zr-area" viewBox="0 0 1000 300" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="zrgB" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#38a8f8" stop-opacity=".5"/><stop offset="1" stop-color="#38a8f8" stop-opacity="0"/></linearGradient>
                        <linearGradient id="zrgR" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#22c597" stop-opacity=".5"/><stop offset="1" stop-color="#22c597" stop-opacity="0"/></linearGradient>
                    </defs>
                    <path d="{{ $aR }}" fill="url(#zrgR)"/>
                    <path d="{{ $aB }}" fill="url(#zrgB)"/>
                    <path d="{{ $dR }}" fill="none" stroke="#22c597" stroke-width="3.5" stroke-linecap="round" style="filter:drop-shadow(0 6px 8px rgba(34,197,151,.45))"/>
                    <path d="{{ $dB }}" fill="none" stroke="#38a8f8" stroke-width="3.5" stroke-linecap="round" style="filter:drop-shadow(0 6px 8px rgba(56,168,248,.45))"/>
                </svg>
                @foreach($months as $i => $m)
                    @if($m['returned'] > 0)<i class="zr-pt" style="--i:{{ $i }};--c:#22c597;left:{{ round(($i + .5) / $nM * 100, 3) }}%;bottom:{{ $bt($m['returned'], $maxI) }}%"></i>@endif
                    @if($m['borrowed'] > 0)<i class="zr-pt" style="--i:{{ $i }};--c:#38a8f8;left:{{ round(($i + .5) / $nM * 100, 3) }}%;bottom:{{ $bt($m['borrowed'], $maxI) }}%"></i>@endif
                @endforeach
                @foreach($months as $i => $m)
                    <div class="zr-hit" style="left:{{ round($i / $nM * 100, 3) }}%;width:{{ round(100 / $nM, 3) }}%">
                        <div class="zr-tip"><b>{{ $m['label'] }} {{ $m['year'] }}</b><br>Total diajukan: {{ $m['total'] }}<br>Masih dipinjam: {{ $m['borrowed'] }}<br>Sudah kembali: {{ $m['returned'] }}@if($m['other'])<br>Lainnya: {{ $m['other'] }}@endif</div>
                    </div>
                @endforeach
            </div>
            <div class="zr-xl">
                @foreach($months as $m)<span>{{ $m['label'] }}<small>{{ $m['year'] }}</small></span>@endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="zr-sec" style="--a1:#fb6f92;--a2:#f7a1c4;--sh:rgba(251,111,146,.45);--gl:rgba(255,160,190,.3)">
        <div class="zr-sh">
            <span class="zr-tag">LAPORAN 2</span><h3>Denda per Bulan</h3>
        </div>
        <p class="zr-sd">Denda keterlambatan dari buku yang sudah dikembalikan, dikelompokkan menurut bulan pengajuan.</p>
        @if($totalFine <= 0)
        <div class="zr-clean" style="--a1:#22c597;--a2:#5ddcc0;--sh:rgba(34,197,151,.5)">
            <div class="zr-bigdn">
                <svg viewBox="0 0 36 36"><defs><linearGradient id="zrcl" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#22c597"/><stop offset="1" stop-color="#7ee7d0"/></linearGradient></defs>
                    <circle class="orb" cx="18" cy="18" r="19.4"/><circle class="trk" cx="18" cy="18" r="15.9155"/><circle class="arc full" cx="18" cy="18" r="15.9155" stroke="url(#zrcl)"/></svg>
                <div class="zr-spin"><i style="--a:0deg"></i><i style="--a:120deg"></i><i style="--a:240deg"></i></div>
                <div class="zr-core"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m5.5 12.8 4.2 4.2 8.8-9.6"/></svg></div>
            </div>
            <div class="zr-cleant">
                <h4>Bebas denda!</h4>
                <p>Tidak ada denda pada periode ini. Semua siswa mengembalikan buku tepat waktu.</p>
                <div class="zr-mini3">
                    <div><small>Total denda</small><b>Rp 0</b></div>
                    <div><small>Sudah kembali</small><b>{{ $ret }} buku</b></div>
                    <div><small>Masih dipinjam</small><b>{{ $bor }} buku</b></div>
                </div>
            </div>
        </div>
        @else
        <div class="zr-finegrid">
            <div class="zr-dcard" style="--a1:#22c597;--a2:#fb6f92;--sh:rgba(251,111,146,.4)">
                <div class="zr-bigdn">
                    <svg viewBox="0 0 36 36"><defs>
                        <linearGradient id="zrfp" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#22c597"/><stop offset="1" stop-color="#7ee7d0"/></linearGradient>
                        <linearGradient id="zrfu" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fb6f92"/><stop offset="1" stop-color="#ffb3cc"/></linearGradient></defs>
                        <circle class="orb" cx="18" cy="18" r="19.4"/><circle class="trk" cx="18" cy="18" r="15.9155"/>
                        @if($ppF > 0)<circle class="arc" cx="18" cy="18" r="15.9155" stroke="url(#zrfp)" stroke-linecap="{{ $gapF ? 'round' : 'butt' }}" style="stroke-dasharray:{{ $dPaid }} 100;stroke-dashoffset:{{ $oPaid }}"/>@endif
                        @if($puF > 0)<circle class="arc" cx="18" cy="18" r="15.9155" stroke="url(#zrfu)" stroke-linecap="{{ $gapF ? 'round' : 'butt' }}" style="stroke-dasharray:{{ $dUnp }} 100;stroke-dashoffset:-{{ $oUnp }};animation-delay:.35s"/>@endif
                    </svg>
                    <div class="zr-core flat"><small>Total denda</small><b>{{ $short($totalFine) }}</b></div>
                </div>
                <ul class="zr-dl2">
                    <li style="--c:#22c597"><i></i>Sudah dibayar<span>{{ $L::rupiah($paidFine) }}<em>{{ $pct($paidFine, $totalFine) }}%</em></span></li>
                    <li style="--c:#fb6f92"><i></i>Belum dibayar<span>{{ $L::rupiah($unpaidFine) }}<em>{{ $pct($unpaidFine, $totalFine) }}%</em></span></li>
                </ul>
            </div>
            <div style="min-width:0">
                <div class="zr-legend" style="margin:0 0 10px"><span style="--c:#22c597"><i></i>Sudah dibayar</span><span style="--c:#fb6f92"><i></i>Belum dibayar</span></div>
        <div class="zr-chart">
            <div class="zr-body">
                @foreach([0, 1, 2, 3, 4] as $k)
                    <div class="zr-grid {{ $k === 0 ? 'zr-base' : '' }}" style="bottom:{{ $bt($maxR * $k / 4, $maxR) }}%"></div>
                    <span class="zr-yl" style="bottom:{{ $bt($maxR * $k / 4, $maxR) }}%">{{ $k === 0 ? '0' : $short($maxR * $k / 4) }}</span>
                @endforeach
                <svg class="zr-area" viewBox="0 0 1000 300" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="zrgP" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#22c597" stop-opacity=".5"/><stop offset="1" stop-color="#22c597" stop-opacity="0"/></linearGradient>
                        <linearGradient id="zrgU" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fb6f92" stop-opacity=".5"/><stop offset="1" stop-color="#fb6f92" stop-opacity="0"/></linearGradient>
                    </defs>
                    <path d="{{ $aP }}" fill="url(#zrgP)"/>
                    <path d="{{ $aU }}" fill="url(#zrgU)"/>
                    <path d="{{ $dP }}" fill="none" stroke="#22c597" stroke-width="3.5" stroke-linecap="round" style="filter:drop-shadow(0 6px 8px rgba(34,197,151,.45))"/>
                    <path d="{{ $dU }}" fill="none" stroke="#fb6f92" stroke-width="3.5" stroke-linecap="round" style="filter:drop-shadow(0 6px 8px rgba(251,111,146,.45))"/>
                </svg>
                @foreach($months as $i => $m)
                    @php $pd = max(0, $m['fine'] - $m['unpaid']); @endphp
                    @if($pd > 0)<i class="zr-pt" style="--i:{{ $i }};--c:#22c597;left:{{ round(($i + .5) / $nM * 100, 3) }}%;bottom:{{ $bt($pd, $maxR) }}%"></i>@endif
                    @if($m['unpaid'] > 0)<i class="zr-pt" style="--i:{{ $i }};--c:#fb6f92;left:{{ round(($i + .5) / $nM * 100, 3) }}%;bottom:{{ $bt($m['unpaid'], $maxR) }}%"></i>@endif
                @endforeach
                @foreach($months as $i => $m)
                    <div class="zr-hit" style="left:{{ round($i / $nM * 100, 3) }}%;width:{{ round(100 / $nM, 3) }}%">
                        <div class="zr-tip"><b>{{ $m['label'] }} {{ $m['year'] }}</b><br>Total denda: {{ $L::rupiah($m['fine']) }}<br>Belum dibayar: {{ $L::rupiah($m['unpaid']) }}</div>
                    </div>
                @endforeach
            </div>
            <div class="zr-xl">
                @foreach($months as $m)<span>{{ $m['label'] }}<small>{{ $m['year'] }}</small></span>@endforeach
            </div>
        </div>
            </div>
        </div>
        @endif
    </div>

    <div class="zr-sec" style="--a1:#38a8f8;--a2:#7c8cff;--sh:rgba(56,168,248,.45);--gl:rgba(125,190,255,.3)">
        <div class="zr-sh"><span class="zr-tag">LAPORAN 3</span><h3>Buku Paling Sering Dipinjam</h3></div>
        <p class="zr-sd">Lima judul favorit siswa pada periode ini. Lingkaran menunjukkan porsinya dari seluruh peminjaman.</p>
        @if(count($top) === 0)
            <div class="zr-empty"><div class="gem"><svg viewBox="0 0 24 24" fill="#fff" stroke="#fff" stroke-width="1" stroke-linejoin="round"><path d="M12 2.5 14.2 9.8 21.5 12l-7.3 2.2L12 21.5l-2.2-7.3L2.5 12l7.3-2.2L12 2.5Z"/></svg></div><b>Belum ada data buku</b>Data muncul setelah ada peminjaman.</div>
        @else
            <div class="zr-podium">
                @foreach($top as $i => $t)
                    @php $cv = $t['cover'] ? asset('storage/' . $t['cover']) : null; @endphp
                    <div class="zr-bk {{ $i === 0 ? 'first' : '' }} r{{ $i + 1 }}" style="--i:{{ $i }}">
                        <span class="zr-rank">{{ $i === 0 ? 'Terfavorit' : '#' . ($i + 1) }}</span>
                        <div class="zr-cv">
                            @if($cv)<img src="{{ $cv }}" alt="" loading="lazy">@else<span>{{ strtoupper(mb_substr($t['title'], 0, 1)) }}</span>@endif
                        </div>
                        <div class="zr-bi">
                            <div class="zr-bt">{{ $t['title'] }}</div>
                            <div class="zr-ba">{{ $t['author'] !== '' ? $t['author'] : 'Penulis tidak diketahui' }}</div>
                            <div class="zr-bn"><b>{{ $t['n'] }}</b> kali dipinjam</div>
                        </div>
                        <div class="zr-share">
                            <svg viewBox="0 0 36 36" style="--p:{{ $t['pct'] }}"><defs><linearGradient id="zrs{{ $i }}" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#8b5cf6"/><stop offset="1" stop-color="#f283c0"/></linearGradient></defs><circle class="trk" cx="18" cy="18" r="15.9155"/><circle class="arc" cx="18" cy="18" r="15.9155" stroke="url(#zrs{{ $i }})"/></svg>
                            <b>{{ $t['pct'] }}%</b>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="zr-sec" style="--a1:#8b5cf6;--a2:#d46bd0;--sh:rgba(139,92,246,.5);--gl:rgba(167,139,250,.28)">
        <div class="zr-sh"><span class="zr-tag">DATA LENGKAP</span><h3>Rincian Setiap Peminjaman</h3></div>
        <p class="zr-sd">Daftar siapa meminjam buku apa. Data yang sama juga ada di file PDF.</p>
        <div class="zr-search">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/></svg>
            <input type="search" id="zrq" placeholder="Cari nama siswa, NISN, atau judul buku..." autocomplete="off">
            <span id="zrc">{{ count($loans) }} data</span>
        </div>
        <div class="zr-head"><span>Siswa</span><span>Buku</span><span>Linimasa</span><span>Status</span></div>
        <div class="zr-rows" id="zrrows">
        @forelse($loans as $l)
            @php
                $fine = $l->current_fine;
                $stc = strtolower($l->status);
                $nm = (string) ($l->user->name ?? '-');
                $code = $l->copy ? $l->copy->copy_code : '-';
            @endphp
            <div class="zr-row s-{{ $stc }} {{ $fine > 0 ? 'has-fine' : '' }}" data-q="{{ strtolower($nm . ' ' . ($l->user->nisn ?? '') . ' ' . ($l->book->title ?? '') . ' ' . $code) }}">
                <div class="zr-who"><span class="zr-av">{{ strtoupper(mb_substr($nm, 0, 1)) }}</span><div><b>{{ $nm }}</b><small>NISN {{ $l->user->nisn ?: '-' }}</small></div></div>
                <div class="zr-bk2"><b>{{ $l->book->title }}</b><small>Kode {{ $code }}</small></div>
                <div class="zr-tl">
                    <div><small>Diajukan</small>{{ $l->requested_at ? $l->requested_at->format('d M Y') : '-' }}</div>
                    <div><small>Jatuh tempo</small>{{ $l->due_date ? $l->due_date->format('d M Y') : '-' }}</div>
                    <div><small>Dikembalikan</small>{{ $l->returned_at ? $l->returned_at->format('d M Y') : '-' }}</div>
                </div>
                <div class="zr-state">
                    <span class="tl-st st-{{ $stc }}">{{ $l->label }}</span>
                    @if($fine > 0)<span class="zr-fn">{{ $L::rupiah($fine) }}<em>{{ $l->status === 'Borrowed' ? 'denda berjalan' : ($l->fine_paid_at ? 'lunas' : 'belum dibayar') }}</em></span>@endif
                </div>
            </div>
        @empty
            <div class="zr-empty"><div class="gem"><svg viewBox="0 0 24 24" fill="#fff" stroke="#fff" stroke-width="1" stroke-linejoin="round"><path d="M12 2.5 14.2 9.8 21.5 12l-7.3 2.2L12 21.5l-2.2-7.3L2.5 12l7.3-2.2L12 2.5Z"/></svg></div><b>Belum ada data</b>Tidak ada peminjaman pada periode ini.</div>
        @endforelse
        <div class="zr-none" id="zrn" hidden>Tidak ada yang cocok dengan pencarianmu.</div>
        </div>
        <div class="zr-pg">{{ $loans->links() }}</div>
    </div>
<script>
(function () {
    var q = document.getElementById('zrq'); if (!q) return;
    var rows = [].slice.call(document.querySelectorAll('#zrrows .zr-row')), c = document.getElementById('zrc'), n = document.getElementById('zrn');
    q.addEventListener('input', function () {
        var v = q.value.trim().toLowerCase(), shown = 0;
        rows.forEach(function (r) { var ok = !v || r.getAttribute('data-q').indexOf(v) > -1; r.style.display = ok ? '' : 'none'; if (ok) shown++; });
        c.textContent = shown + ' data'; n.hidden = shown !== 0;
    });
})();
</script>
</div>
</x-admin-layout>