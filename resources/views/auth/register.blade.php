@php
    $pick = function ($base) {
        foreach (['jpg', 'jpeg', 'png', 'webp', 'svg'] as $e) {
            $f = 'img/landing/' . $base . '.' . $e;
            if (file_exists(public_path($f))) return asset($f) . '?v=' . filemtime(public_path($f));
        }
        return null;
    };
    $logo = $pick('logo');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Daftar | Trafie-library</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{--ink:#2a2d63;--ink2:#5b5f8a;--muted:#8a8db0;--purple:#9a7fd6;--grad:linear-gradient(135deg,#bb99e8 0%,#8d79da 100%)}
*{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:'Poppins',sans-serif;color:var(--ink);min-height:100vh;overflow-x:hidden;background:#efe8fb}
a{color:inherit;text-decoration:none}
.bgfx{position:fixed;inset:0;z-index:-1;overflow:hidden;background:linear-gradient(160deg,#efe8fb,#faf1fa 45%,#e9e8fb)}
.bgfx i{position:absolute;border-radius:50%;filter:blur(80px);opacity:.85;animation:drift 20s ease-in-out infinite alternate}
.bgfx i:nth-child(1){width:560px;height:560px;left:-160px;top:20%;background:#f6c4e0}
.bgfx i:nth-child(2){width:520px;height:520px;right:-140px;top:-80px;background:#c3d0fa;animation-delay:-6s}
.bgfx i:nth-child(3){width:460px;height:460px;left:34%;bottom:-120px;background:#d8c6f7;animation-delay:-11s}
.bgfx i:nth-child(4){width:420px;height:420px;right:8%;bottom:10%;background:#f8cbe5;animation-delay:-3s}
@keyframes drift{to{transform:translate(70px,-50px) scale(1.12)}}
@keyframes rise{from{opacity:0;transform:translateY(24px)}to{opacity:1;transform:none}}
@keyframes bob{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}
@keyframes twinkle{0%,100%{opacity:.2;transform:scale(.6)}50%{opacity:1;transform:scale(1.2)}}
@keyframes sweep{0%{left:-70%}55%,100%{left:140%}}
@keyframes pulse{0%,100%{opacity:.55;transform:scale(.95)}50%{opacity:1;transform:scale(1.08)}}

.stage{position:relative;max-width:1240px;margin:0 auto;min-height:100vh;display:grid;grid-template-columns:minmax(0,1fr) 440px;align-items:center;gap:24px;padding:28px}
.illus{position:relative;align-self:stretch;display:flex;align-items:flex-end;justify-content:center;min-height:540px;perspective:1300px}
.float{position:relative;width:100%;height:min(88vh,700px);animation:bob 6.5s ease-in-out infinite}
.halo{position:absolute;left:50%;top:52%;width:76%;height:70%;transform:translate(-50%,-50%);border-radius:50%;pointer-events:none;mix-blend-mode:screen;
  background:radial-gradient(circle,rgba(255,255,255,.95),rgba(236,214,255,.5) 45%,transparent 70%);animation:pulse 4.5s ease-in-out infinite}
.art{position:relative;width:100%;height:100%;object-fit:contain;object-position:center bottom;display:block;
  transform:rotateY(calc(var(--mx,0) * -9deg)) rotateX(calc(var(--my,0) * 6deg)) translate(calc(var(--mx,0) * 10px),calc(var(--my,0) * 6px));
  transition:transform .25s ease-out;transform-style:preserve-3d;
  filter:drop-shadow(0 28px 30px rgba(90,70,170,.38)) drop-shadow(0 0 30px rgba(255,255,255,.75))}
.gl{position:absolute;width:26px;height:26px;margin:-13px 0 0 -13px;pointer-events:none;animation:twinkle 2.8s ease-in-out infinite;
  background:radial-gradient(circle,#fff 0 3px,rgba(255,255,255,0) 7px),linear-gradient(#fff,#fff) 50% 50%/1.6px 100% no-repeat,linear-gradient(#fff,#fff) 50% 50%/100% 1.6px no-repeat;
  filter:drop-shadow(0 0 6px #fff) drop-shadow(0 0 12px #e3d0ff)}

.brand{display:flex;align-items:center;gap:10px;justify-content:center}
.brand img{width:50px;height:54px;object-fit:contain;filter:drop-shadow(0 4px 8px rgba(130,110,205,.35))}
.brand b{display:block;font-family:'Cormorant Garamond',serif;font-style:italic;font-weight:600;font-size:28px;line-height:1;white-space:nowrap}
.brand small{display:block;font-size:8.5px;color:var(--purple);margin-top:3px;max-width:140px;line-height:1.3}
.brand.top{position:absolute;top:24px;left:50%;transform:translateX(-50%);z-index:5}
.brand.left{position:absolute;top:30px;left:50%;transform:translateX(-50%);z-index:5}

.panel{position:relative;z-index:4;border-radius:24px;padding:28px 30px 24px;overflow:hidden;animation:rise .9s both;
  background:linear-gradient(145deg,rgba(255,255,255,.88),rgba(255,255,255,.58));border:1px solid rgba(255,255,255,.9);
  backdrop-filter:blur(26px) saturate(1.7);-webkit-backdrop-filter:blur(26px) saturate(1.7);
  box-shadow:0 24px 60px rgba(120,100,200,.28),inset 0 1.5px 0 #fff,inset 0 -1px 0 rgba(170,150,230,.2)}
.panel::before{content:"";position:absolute;top:0;left:-70%;width:45%;height:100%;pointer-events:none;
  background:linear-gradient(100deg,transparent,rgba(255,255,255,.7),transparent);transform:skewX(-20deg);animation:sweep 7s ease-in-out infinite}
.panel h1{font-family:'Cormorant Garamond',serif;font-weight:600;font-size:24px;line-height:1.1}
.panel .sub{font-size:10.5px;color:var(--ink2);margin-top:5px;line-height:1.5}
.tabs{display:grid;grid-template-columns:1fr 1fr;gap:0;margin-top:18px;padding:3px;border-radius:999px;background:rgba(255,255,255,.7);border:1px solid rgba(150,130,210,.25)}
.tabs button{height:30px;border:0;border-radius:999px;background:transparent;font:500 11px 'Poppins';color:var(--ink2);cursor:pointer;transition:.25s}
.tabs button.on{color:#fff;background:linear-gradient(180deg,rgba(255,255,255,.35),rgba(255,255,255,0) 55%),var(--grad);box-shadow:0 6px 14px rgba(143,123,217,.4),inset 0 1px 0 rgba(255,255,255,.6)}
.field{margin-top:14px}
.field label{display:block;font-size:10.5px;font-weight:500;margin-bottom:6px}
.inp{position:relative}
.inp input,.inp select{width:100%;height:36px;padding:0 40px 0 12px;border-radius:10px;border:1px solid rgba(150,130,210,.35);background:rgba(255,255,255,.85);font:400 11px 'Poppins';color:var(--ink);outline:0;transition:.2s;box-shadow:inset 0 1px 3px rgba(150,130,210,.12)}
.inp input::placeholder{color:#a3a6c4}
.inp input:focus,.inp select:focus{border-color:var(--purple);box-shadow:0 0 0 3px rgba(154,127,214,.22),0 0 18px rgba(186,160,240,.4)}
.eye{position:absolute;right:10px;top:50%;transform:translateY(-50%);width:22px;height:22px;border:0;background:none;cursor:pointer;display:flex;align-items:center;justify-content:center}
.eye svg{width:16px;height:16px;stroke:var(--ink2);fill:none;stroke-width:1.7}
.em{display:block;color:#c0425f;font-size:9.5px;margin-top:4px}
.row{display:flex;align-items:center;justify-content:space-between;margin-top:14px;font-size:10px;color:var(--ink2)}
.chk{display:flex;align-items:center;gap:8px;font-size:10px;color:var(--ink2);cursor:pointer}
.chk input{width:14px;height:14px;accent-color:#8d79da}
.chk a{color:var(--purple);text-decoration:underline}
.row a:hover{color:var(--purple)}
.main{position:relative;overflow:hidden;width:100%;height:40px;margin-top:16px;border:0;border-radius:12px;color:#fff;font:500 13px 'Poppins';cursor:pointer;
  background:linear-gradient(180deg,rgba(255,255,255,.38),rgba(255,255,255,0) 52%),var(--grad);box-shadow:0 10px 24px rgba(143,123,217,.45),inset 0 1px 0 rgba(255,255,255,.65);transition:.25s}
.main::after{content:"";position:absolute;top:0;left:-60%;width:40%;height:100%;background:linear-gradient(100deg,transparent,rgba(255,255,255,.6),transparent);transform:skewX(-20deg);transition:left .6s}
.main:hover{transform:translateY(-2px);box-shadow:0 14px 30px rgba(143,123,217,.55),inset 0 1px 0 rgba(255,255,255,.65)}
.main:hover::after{left:130%}
.alt{text-align:center;font-size:10px;color:var(--ink2);margin-top:16px}
.alt a{color:var(--ink);font-weight:500}
.alt a:hover{color:var(--purple)}
.msg{margin-top:14px;padding:9px 12px;border-radius:10px;font-size:10.5px;line-height:1.45}
.msg.err{background:rgba(255,226,233,.85);border:1px solid rgba(230,130,160,.5);color:#a8304f}
.msg.ok{background:rgba(224,247,235,.85);border:1px solid rgba(110,190,150,.5);color:#2c7a56}
@media (max-width:960px){
  .stage{grid-template-columns:1fr;max-width:480px;padding:20px 16px}
  .illus{display:none}
  .brand.top,.brand.left{position:static;transform:none;margin-bottom:16px}
}
@media (prefers-reduced-motion:reduce){*{animation:none!important}}

/* ===== V2-19 : ilustrasi 3 lapis, partikel, aura ===== */
.float{transform-style:preserve-3d}
.ly{position:absolute;inset:0;width:100%;height:100%;object-fit:contain;object-position:center bottom;pointer-events:none;transition:transform .25s ease-out}
.ly.b{transform:translateZ(-80px) scale(1.14) translate(calc(var(--mx,0) * -16px),calc(var(--my,0) * -9px));filter:drop-shadow(0 0 26px rgba(255,255,255,.65))}
.ly.m{transform:translate(calc(var(--mx,0) * 12px),calc(var(--my,0) * 7px)) rotateY(calc(var(--mx,0) * -6deg));filter:drop-shadow(0 32px 28px rgba(90,70,170,.42)) drop-shadow(0 0 32px rgba(255,255,255,.75))}
.ly.f{transform:translateZ(110px) scale(.93) translate(calc(var(--mx,0) * 34px),calc(var(--my,0) * 20px));filter:drop-shadow(0 16px 14px rgba(90,70,170,.38))}
.pw{position:relative;z-index:4}
.pw::before{content:"";position:absolute;inset:-16px;border-radius:38px;z-index:-1;pointer-events:none;
  background:radial-gradient(ellipse at 50% 50%,rgba(200,170,250,.5),transparent 70%);filter:blur(18px);animation:pulse 5s ease-in-out infinite}
.pw .edge{position:absolute;pointer-events:none;z-index:6;filter:drop-shadow(0 10px 12px rgba(90,70,170,.32))}
.pw .edge.tr{top:-62px;right:-58px;width:176px;animation:bob 7s ease-in-out infinite}
.pw .edge.bl{bottom:-56px;left:-62px;width:156px;transform:scale(-1,-1)}
.panel{z-index:4}
.field{animation:rise .7s both}
.field:nth-of-type(2){animation-delay:.08s}.field:nth-of-type(3){animation-delay:.16s}.field:nth-of-type(4){animation-delay:.24s}.field:nth-of-type(5){animation-delay:.32s}.field:nth-of-type(6){animation-delay:.4s}
.pt{position:fixed;top:-20px;width:11px;height:15px;border-radius:80% 0 80% 0;background:linear-gradient(135deg,#fde3f0,#f4b6d4);opacity:.75;z-index:1;pointer-events:none;animation:petal var(--d,20s) linear infinite}
@keyframes petal{0%{transform:translate3d(0,-5vh,0) rotate(0) scale(var(--s,1))}50%{transform:translate3d(46px,55vh,0) rotate(180deg) scale(var(--s,1))}100%{transform:translate3d(-30px,112vh,0) rotate(360deg) scale(var(--s,1))}}
.sfs{position:fixed;inset:0;pointer-events:none;z-index:0;filter:drop-shadow(0 0 5px #fff) drop-shadow(0 0 10px #e6d4ff)}
.sf{position:absolute;width:11px;height:11px;background:#fff;clip-path:polygon(50% 0,60% 40%,100% 50%,60% 60%,50% 100%,40% 60%,0 50%,40% 40%);animation:twinkle 3.2s ease-in-out infinite}
@media (max-width:960px){.pw .edge.tr{width:112px;top:-38px;right:-12px}.pw .edge.bl{width:96px;bottom:-34px;left:-12px}}
@media (prefers-reduced-motion:reduce){.pt,.sf,.pw::before,.pw .edge{animation:none}}

/* ===== V2-20: penghalusan ===== */
/* 1. Ilustrasi: kotak gambar = gambar asli, lalu keempat tepinya dilebur ke latar */
.ly.m { inset: auto auto 0 50%; width: auto; height: 100%; max-width: none;
  transform: translateX(-50%) translate(calc(var(--mx,0) * 12px), calc(var(--my,0) * 7px)) rotateY(calc(var(--mx,0) * -6deg));
  filter: none;
  -webkit-mask-image: linear-gradient(to right, transparent, #000 15%, #000 85%, transparent), linear-gradient(to bottom, transparent, #000 14%, #000 88%, transparent);
  -webkit-mask-composite: source-in;
  mask-image: linear-gradient(to right, transparent, #000 15%, #000 85%, transparent), linear-gradient(to bottom, transparent, #000 14%, #000 88%, transparent);
  mask-composite: intersect; }
.halo { opacity: .9; }

/* 2. Bunga di pojok kartu: kotak putih hilang (multiply) dan tepinya lembut */
.pw { z-index: auto; }
.pw .edge { filter: none; mix-blend-mode: multiply;
  -webkit-mask-image: radial-gradient(ellipse at 50% 50%, #000 52%, transparent 100%);
  mask-image: radial-gradient(ellipse at 50% 50%, #000 52%, transparent 100%); }

/* 3. Autofill browser tidak lagi biru */
.inp input:-webkit-autofill, .inp input:-webkit-autofill:hover, .inp input:-webkit-autofill:focus, .inp select:-webkit-autofill {
  -webkit-text-fill-color: #2a2d63; caret-color: #2a2d63;
  -webkit-box-shadow: 0 0 0 40px #faf6ff inset !important; box-shadow: 0 0 0 40px #faf6ff inset !important;
  transition: background-color 9999s ease-out 0s; }

/* 4. Kartu kaca, input, dan tombol lebih halus */
.panel { border-radius: 28px;
  background: linear-gradient(145deg, rgba(255,255,255,.84), rgba(250,238,255,.56));
  box-shadow: 0 32px 70px rgba(120,100,200,.26), inset 0 1.5px 0 #fff, inset 0 -1px 0 rgba(170,150,230,.18); }
.panel h1 { font-size: 26px; letter-spacing: .2px; }
.inp input, .inp select { height: 40px; border-radius: 14px; font-size: 11.5px; background: rgba(255,255,255,.9); }
.inp input:focus, .inp select:focus { border-color: #b79cf0; box-shadow: 0 0 0 4px rgba(183,156,240,.22), 0 6px 20px rgba(186,160,240,.35); }
.tabs { padding: 4px; background: rgba(255,255,255,.75); }
.tabs button { height: 32px; font-size: 11.5px; }
.main { height: 44px; border-radius: 14px; font-size: 13.5px; letter-spacing: .3px;
  background: linear-gradient(180deg, rgba(255,255,255,.36), rgba(255,255,255,0) 55%), linear-gradient(135deg, #b79cf0 0%, #c69be9 52%, #ee9fcb 100%);
  box-shadow: 0 12px 26px rgba(170,130,225,.42), inset 0 1px 0 rgba(255,255,255,.7); }
.alt a { color: #7a5fc4; }
/* ===== V2-21: kotak SVG diperluas, jadi tidak ada tepi potong ===== */
.ly { scale: 1.5; transform-origin: 50% 100%; }
.ly.m { inset: 0; width: 100%; height: 100%;
  -webkit-mask-image: none; mask-image: none;
  transform: translate(calc(var(--mx,0) * 12px), calc(var(--my,0) * 7px)) rotateY(calc(var(--mx,0) * -6deg));
  filter: drop-shadow(0 32px 28px rgba(90,70,170,.4)) drop-shadow(0 0 30px rgba(255,255,255,.7)); }
.pw .edge { mix-blend-mode: normal; -webkit-mask-image: none; mask-image: none; filter: drop-shadow(0 12px 14px rgba(120,90,190,.24)); }
.pw .edge.tr { top: -104px; right: -112px; width: 290px; transform: none; }
.pw .edge.bl { bottom: -100px; left: -112px; width: 260px; transform: scale(-1, -1); animation: none; }
.pw .edge.tl { top: -64px; left: -72px; width: 150px; transform: scale(-1, 1) rotate(-8deg); opacity: .97; }
.pw .edge.br { bottom: -62px; right: -70px; width: 145px; transform: scale(1, -1) rotate(8deg); opacity: .97; }
@media (max-width: 960px) {
  .pw .edge.tr { width: 190px; top: -64px; right: -42px; }
  .pw .edge.bl { width: 170px; bottom: -60px; left: -42px; }
  .pw .edge.tl, .pw .edge.br { display: none; }
}
/* ===== V2-22: logo ke pojok, ilustrasi tidak melebar ===== */
.brand.top, .brand.left { top: 22px; left: 34px; right: auto; transform: none; justify-content: flex-start; }
.brand.top img, .brand.left img { width: 44px; height: 48px; }
.brand.top b, .brand.left b { font-size: 25px; }
.ly { scale: 1.18; transform-origin: 50% 100%; }
.float { height: min(86vh, 680px); }
@media (max-width: 960px) {
  .brand.top, .brand.left { position: static; margin-bottom: 14px; justify-content: center; }
}
/* ===== V2-23: logo di pojok layar, bunga besar lebih besar ===== */
.brand.top, .brand.left { position: fixed; top: 16px; left: 22px; right: auto; transform: none; z-index: 20; }
.pw .edge.tr { width: 335px; top: -122px; right: -132px; }
.pw .edge.bl { width: 300px; bottom: -116px; left: -130px; }
@media (max-width: 960px) {
  .brand.top, .brand.left { position: static; }
  .pw .edge.tr { width: 210px; top: -70px; right: -46px; }
  .pw .edge.bl { width: 190px; bottom: -66px; left: -46px; }
}
/* ===== V2-26: logo di pojok layar, form lebih ringkas ===== */
.brand.top, .brand.left { position: fixed !important; top: 16px !important; left: 22px !important; right: auto !important; transform: none !important; z-index: 20; }
.pw .panel { padding: 22px 28px 18px; }
.pw .panel .sub { margin-top: 3px; }
.pw .field { margin-top: 9px; }
.pw .field label { margin-bottom: 4px; }
.pw .inp input, .pw .inp select { height: 34px; }
.pw .row { margin-top: 12px; }
.pw .main { height: 38px; margin-top: 12px; }
.pw .alt { margin-top: 12px; }
@media (max-width: 960px) { .brand.top, .brand.left { position: static !important; margin: 0 0 16px !important; } }</style>
    <!-- trafie-icon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=3">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}?v=3">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=3">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=3">
    <meta name="theme-color" content="#a78bfa">
    <!-- /trafie-icon -->
</head>
<body>
<div class="bgfx"><i></i><i></i><i></i><i></i></div><div class="sfs"><i class='sf' style='left:22%;top:22%;animation-delay:0.8s;width:15px;height:15px'></i><i class='sf' style='left:30%;top:31%;animation-delay:0.5s;width:10px;height:10px'></i><i class='sf' style='left:94%;top:78%;animation-delay:0.1s;width:15px;height:15px'></i><i class='sf' style='left:79%;top:45%;animation-delay:0.8s;width:15px;height:15px'></i><i class='sf' style='left:6%;top:23%;animation-delay:3s;width:9px;height:9px'></i><i class='sf' style='left:23%;top:55%;animation-delay:1.2s;width:13px;height:13px'></i><i class='sf' style='left:17%;top:11%;animation-delay:0.4s;width:7px;height:7px'></i><i class='sf' style='left:77%;top:38%;animation-delay:0.4s;width:12px;height:12px'></i><i class='sf' style='left:66%;top:75%;animation-delay:2.1s;width:14px;height:14px'></i><i class='sf' style='left:2%;top:48%;animation-delay:2s;width:6px;height:6px'></i><i class='sf' style='left:19%;top:67%;animation-delay:2.5s;width:6px;height:6px'></i><i class='sf' style='left:80%;top:74%;animation-delay:2.4s;width:16px;height:16px'></i><i class='sf' style='left:25%;top:63%;animation-delay:1.4s;width:7px;height:7px'></i><i class='sf' style='left:95%;top:38%;animation-delay:0.5s;width:13px;height:13px'></i><i class='sf' style='left:77%;top:57%;animation-delay:2.1s;width:8px;height:8px'></i><i class='sf' style='left:20%;top:93%;animation-delay:1.9s;width:12px;height:12px'></i><i class='sf' style='left:66%;top:41%;animation-delay:0.7s;width:6px;height:6px'></i><i class='sf' style='left:74%;top:51%;animation-delay:2.4s;width:11px;height:11px'></i><i class='sf' style='left:53%;top:88%;animation-delay:0.3s;width:11px;height:11px'></i><i class='sf' style='left:72%;top:36%;animation-delay:2.5s;width:10px;height:10px'></i><i class='sf' style='left:28%;top:19%;animation-delay:2.8s;width:7px;height:7px'></i><i class='sf' style='left:90%;top:6%;animation-delay:1.9s;width:9px;height:9px'></i></div><i class='pt' style='left:73%;--d:24s;--s:1.31;animation-delay:-13s'></i><i class='pt' style='left:66%;--d:16s;--s:1.4;animation-delay:-23s'></i><i class='pt' style='left:66%;--d:27s;--s:1.18;animation-delay:-7s'></i><i class='pt' style='left:24%;--d:16s;--s:0.89;animation-delay:-10s'></i><i class='pt' style='left:9%;--d:21s;--s:0.99;animation-delay:-5s'></i><i class='pt' style='left:61%;--d:20s;--s:0.92;animation-delay:-10s'></i><i class='pt' style='left:49%;--d:27s;--s:1.12;animation-delay:-20s'></i><i class='pt' style='left:68%;--d:19s;--s:1.06;animation-delay:-24s'></i><i class='pt' style='left:56%;--d:20s;--s:1.44;animation-delay:-5s'></i><i class='pt' style='left:92%;--d:19s;--s:1.48;animation-delay:-9s'></i><i class='pt' style='left:36%;--d:20s;--s:1.34;animation-delay:-3s'></i><i class='pt' style='left:82%;--d:27s;--s:1.2;animation-delay:-18s'></i><i class='pt' style='left:40%;--d:23s;--s:1.1;animation-delay:-13s'></i><i class='pt' style='left:31%;--d:29s;--s:1.03;animation-delay:-18s'></i>
<main class="stage" id="stage">
  <a href="{{ url('/') }}" class="brand left">
    @if($logo)<img src="{{ $logo }}" alt="">@endif
    <span><b>Trafie-library</b><small>Where Every Book Blooms Into a Universe</small></span>
  </a>

  <!-- auth-v19 -->
<div class="illus">
    <div class="float">
      <div class="halo"></div>
      @php $mid = $pick('auth-register'); $layered = $mid && str_contains($mid, '.svg'); @endphp
      @if($layered && $pick('auth-register-back'))<img class="ly b" src="{{ $pick('auth-register-back') }}" alt="">@endif
      @if($mid)<img class="ly m" src="{{ $mid }}" alt="">@endif
      @if($layered && $pick('auth-register-front'))<img class="ly f" src="{{ $pick('auth-register-front') }}" alt="">@endif
      <i class="gl" style="left:42%;top:10%"></i><i class="gl" style="left:72%;top:18%;animation-delay:.9s"></i><i class="gl" style="left:26%;top:32%;animation-delay:1.7s"></i><i class="gl" style="left:58%;top:44%;animation-delay:.4s"></i><i class="gl" style="left:82%;top:60%;animation-delay:2.1s"></i><i class="gl" style="left:16%;top:70%;animation-delay:1.2s"></i><i class="gl" style="left:48%;top:26%;animation-delay:2.6s"></i>
    </div>
  </div>

  <div class="pw">
    <section class="panel">
    <h1>Daftar Akun Siswa</h1>
    <p class="sub">Bergabunglah dengan Trafie-library dan mulailah petualangan membaca</p>

    @if($errors->any())<div class="msg err">{{ $errors->first() }}</div>@endif

    <form method="POST" action="{{ route('register') }}">
      @csrf
      
      <div class="field">
  <label for="f_nisn">NISN</label>
  <div class="inp"><input id="f_nisn" type="text" name="nisn" value="{{ old('nisn') }}" placeholder="Masukkan NISN (harus unik)" autocomplete="off" required></div>
  @error('nisn')<small class="em">{{ $message }}</small>@enderror
</div><div class="field">
  <label for="f_name">Nama Lengkap</label>
  <div class="inp"><input id="f_name" type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" autocomplete="name" required></div>
  @error('name')<small class="em">{{ $message }}</small>@enderror
</div><div class="field">
  <label for="f_email">Email</label>
  <div class="inp"><input id="f_email" type="email" name="email" value="{{ old('email') }}" placeholder="Masukkan email (harus unik)" autocomplete="username" required></div>
  @error('email')<small class="em">{{ $message }}</small>@enderror
</div><div class="field">
  <label for="f_password">Password</label>
  <div class="inp"><input id="f_password" type="password" name="password" placeholder="Buat password" autocomplete="new-password" required><button type="button" class="eye" onclick="tg(this)" aria-label="Lihat password"><svg viewBox="0 0 24 24"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg></button></div>
  @error('password')<small class="em">{{ $message }}</small>@enderror
</div><div class="field">
  <label for="f_password_confirmation">Konfirmasi Password</label>
  <div class="inp"><input id="f_password_confirmation" type="password" name="password_confirmation" placeholder="Konfirmasi password" autocomplete="new-password" required><button type="button" class="eye" onclick="tg(this)" aria-label="Lihat password"><svg viewBox="0 0 24 24"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg></button></div>
  @error('password_confirmation')<small class="em">{{ $message }}</small>@enderror
</div>
      <div class="row" style="margin-top:16px">
        <label class="chk"><input type="checkbox" name="terms" value="1" required> <span>Saya menyetujui <a href="#" onclick="return false">Syarat dan Ketentuan</a></span></label>
      </div>
      <button type="submit" class="main">Daftar</button>
    </form>
    <p class="alt">Sudah punya akun? <a href="{{ \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login') }}">Login di sini</a></p>
  </section>
    @if($pick('auth-edge'))<img class="edge tr" src="{{ $pick('auth-edge') }}" alt=""><img class="edge bl" src="{{ $pick('auth-edge') }}" alt=""><img class="edge tl" src="{{ $pick('auth-edge') }}" alt=""><img class="edge br" src="{{ $pick('auth-edge') }}" alt="">@endif
  </div>
</main>
<script>
function tg(b){var i=b.parentNode.querySelector('input');i.type=i.type==='password'?'text':'password';}
(function(){
  var s=document.getElementById('stage'); if(!s) return;
  s.addEventListener('mousemove',function(e){var r=s.getBoundingClientRect();
    s.style.setProperty('--mx',((e.clientX-r.left)/r.width-.5).toFixed(3));
    s.style.setProperty('--my',((e.clientY-r.top)/r.height-.5).toFixed(3));});
  s.addEventListener('mouseleave',function(){s.style.setProperty('--mx',0);s.style.setProperty('--my',0);});
})();
</script>
@include('partials.gloss')
@include('partials.skin')
@include('partials.statviz')
</body>
</html>