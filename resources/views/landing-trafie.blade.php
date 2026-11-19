@php
    $img = function ($base) {
        foreach (['jpg', 'jpeg', 'png', 'webp', 'svg'] as $e) {
            $f = 'img/landing/' . $base . '.' . $e;
            if (file_exists(public_path($f))) return asset($f) . '?v=' . filemtime(public_path($f));
        }
        return null;
    };
    $fmt = function ($n) { return number_format((int) $n, 0, ',', '.'); };
    $star = '<svg viewBox="0 0 24 24" width="11" height="11" style="display:inline-block;margin-right:1px"><path d="M12 2l3 6.9 7.4.6-5.6 4.8 1.7 7.2L12 17.8 5.5 21.5l1.7-7.2L1.6 9.5 9 8.9z" fill="#f2b33d"/></svg>';
    $spark = '<svg viewBox="-12 -12 24 24" width="14" height="14" style="display:inline-block;vertical-align:-2px"><path d="M0-12Q1.5-1.5 12 0Q1.5 1.5 0 12Q-1.5 1.5-12 0Q-1.5-1.5 0-12Z" fill="#f2c14e"/></svg>';
    $cats = [
        ['Fiksi', 'c-blue', '<path d="M5 5h6a2 2 0 0 1 2 2v12a2 2 0 0 0-2-2H5zM19 5h-6M19 5v12h-6"/>'],
        ['Romance', 'c-pink', '<path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.5A4 4 0 0 1 19 10c0 5.6-7 10-7 10z"/>'],
        ['Fantasy', 'c-lilac', '<path d="M12 3l2.4 5.6L20 9l-4.4 3.8L17 19l-5-3-5 3 1.4-6.2L4 9l5.6-.4z"/>'],
        ['Non-Fiksi', 'c-green', '<path d="M4 7l8-4 8 4v10l-8 4-8-4zM4 7l8 4 8-4M12 11v10"/>'],
        ['Mystery', 'c-peach', '<circle cx="11" cy="11" r="6"/><path d="M16 16l5 5M9 11a2 2 0 0 1 2-2"/>'],
        ['Thriller', 'c-lilac', '<circle cx="12" cy="12" r="8"/><path d="M12 7v5l3 2"/>'],
        ['Sci-Fi', 'c-blue', '<ellipse cx="12" cy="12" rx="9" ry="3.5" transform="rotate(-30 12 12)"/><circle cx="12" cy="12" r="3"/>'],
        ['Self Development', 'c-green', '<path d="M5 19c0-8 5-14 14-14 0 9-6 14-14 14zM5 19l8-8"/>'],
    ];
    $logoUrl = $img('logo');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Trafie-library | Where Every Book Blooms Into a Universe</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --ink:#2a2d63; --ink2:#5b5f8a; --muted:#8a8db0;
  --purple:#9a7fd6; --line:rgba(150,140,210,.22);
  --grad:linear-gradient(135deg,#bb99e8 0%,#8d79da 100%);
  --glassbg:linear-gradient(145deg,rgba(255,255,255,.74),rgba(255,255,255,.34));
  --glassbd:1px solid rgba(255,255,255,.82);
  --glasssh:0 14px 44px rgba(130,110,205,.18),inset 0 1.5px 0 rgba(255,255,255,.95),inset 0 -1px 0 rgba(170,150,230,.18);
}
*{box-sizing:border-box;margin:0;padding:0}
html{scroll-behavior:smooth}
body{font-family:'Poppins',sans-serif;color:var(--ink);background:#f3eefb;min-height:100vh;overflow-x:hidden}
a{color:inherit;text-decoration:none}
.wrap{max-width:1536px;margin:0 auto;padding:0 24px;position:relative}

/* latar berwarna supaya efek kaca terlihat */
.bgfx{position:fixed;inset:0;z-index:-1;overflow:hidden;background:linear-gradient(160deg,#efe8fb,#faf1fa 45%,#e9e8fb)}
.bgfx i{position:absolute;border-radius:50%;filter:blur(80px);opacity:.8;animation:drift 20s ease-in-out infinite alternate}
.bgfx i:nth-child(1){width:560px;height:560px;left:-160px;top:380px;background:#f6c4e0}
.bgfx i:nth-child(2){width:520px;height:520px;right:-140px;top:300px;background:#c3d0fa;animation-delay:-6s}
.bgfx i:nth-child(3){width:460px;height:460px;left:38%;top:640px;background:#d8c6f7;animation-delay:-11s}
.bgfx i:nth-child(4){width:500px;height:500px;right:6%;bottom:-160px;background:#f8cbe5;animation-delay:-3s}
@keyframes drift{to{transform:translate(70px,-50px) scale(1.12)}}
@keyframes rise{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:none}}
@keyframes twinkle{0%,100%{opacity:.25;transform:scale(.7)}50%{opacity:1;transform:scale(1.15)}}
@keyframes floaty{0%,100%{transform:translateY(0)}50%{transform:translateY(-8px)}}

/* NAV */
.nav{position:absolute;top:10px;left:50%;transform:translateX(-50%);width:calc(100% - 48px);max-width:1450px;height:58px;z-index:20;
  display:flex;align-items:center;gap:28px;padding:0 24px 0 26px;border-radius:999px;
  background:linear-gradient(145deg,rgba(255,255,255,.78),rgba(255,255,255,.48));
  backdrop-filter:blur(26px) saturate(1.8);-webkit-backdrop-filter:blur(26px) saturate(1.8);
  box-shadow:0 10px 34px rgba(130,110,205,.2),inset 0 1.5px 0 rgba(255,255,255,.95);border:1px solid rgba(255,255,255,.9)}
.brand{display:flex;align-items:center;gap:10px;flex-shrink:0}
.brand img,.brand svg{width:44px;height:48px;object-fit:contain}
.brand b{display:block;font-family:'Cormorant Garamond',serif;font-style:italic;font-weight:600;font-size:27px;line-height:1;color:var(--ink);white-space:nowrap}
.brand small{display:block;font-size:8.5px;color:var(--purple);margin-top:3px}
.menu{display:flex;gap:34px;margin-left:56px;font-size:13px}
.menu a{padding:6px 0;color:var(--ink2);position:relative;transition:.2s}
.menu a:hover{color:var(--ink)}
.menu a.on{color:var(--ink);font-weight:500}
.menu a.on::after{content:"";position:absolute;left:0;right:0;bottom:-3px;height:2px;border-radius:2px;background:var(--purple)}
.search{margin-left:auto;display:flex;align-items:center;gap:10px;width:257px;height:34px;padding:0 16px;border-radius:999px;background:rgba(255,255,255,.8);border:1px solid rgba(255,255,255,.95);box-shadow:inset 0 1px 3px rgba(150,130,210,.15)}
.search input{border:0;outline:0;background:transparent;font:400 10.5px 'Poppins';color:var(--ink);width:100%}
.search svg{width:16px;height:16px;stroke:var(--ink2);fill:none;stroke-width:1.8;flex-shrink:0}
.btn{position:relative;overflow:hidden;display:inline-flex;align-items:center;justify-content:center;gap:8px;border-radius:999px;font:500 12.5px 'Poppins';cursor:pointer;border:1px solid transparent;transition:.25s}
.btn-out{height:34px;padding:0 24px;background:rgba(255,255,255,.7);border-color:rgba(150,130,210,.45);color:var(--ink);box-shadow:inset 0 1px 0 #fff}
.btn-fill{height:34px;padding:0 26px;color:#fff;background:linear-gradient(180deg,rgba(255,255,255,.38),rgba(255,255,255,0) 52%),var(--grad);box-shadow:0 8px 20px rgba(143,123,217,.4),inset 0 1px 0 rgba(255,255,255,.65)}
.btn-fill::after{content:"";position:absolute;top:0;left:-60%;width:40%;height:100%;background:linear-gradient(100deg,transparent,rgba(255,255,255,.55),transparent);transform:skewX(-20deg);transition:left .6s}
.btn-fill:hover::after{left:130%}
.btn:hover{transform:translateY(-2px)}
.burger{display:none;margin-left:auto;background:none;border:0;cursor:pointer}
.burger svg{width:26px;height:26px;stroke:var(--ink);fill:none;stroke-width:2}

/* HERO */
.hero{position:relative;height:392px;overflow:hidden;background:linear-gradient(120deg,#d9d1f6 0%,#e7dcf8 45%,#f4d9ee 100%)}
.hero .art{position:absolute;inset:0;background-size:cover;background-position:right center;background-repeat:no-repeat}
.hero .fade{position:absolute;inset:0;background:linear-gradient(90deg,rgba(246,241,253,.78) 0%,rgba(246,241,253,.35) 34%,transparent 58%)}
.hero .tw{position:absolute;z-index:4;animation:twinkle 3.4s ease-in-out infinite}
.hero-in{position:relative;z-index:5;max-width:1536px;margin:0 auto;padding:102px 24px 0 138px}
.hero-in>*{animation:rise .8s both}
.hero-in>*:nth-child(2){animation-delay:.1s}.hero-in>*:nth-child(3){animation-delay:.2s}.hero-in>*:nth-child(4){animation-delay:.3s}
.pill{display:inline-flex;align-items:center;gap:8px;height:26px;padding:0 14px 0 10px;border-radius:999px;background:rgba(255,255,255,.62);backdrop-filter:blur(10px);border:1px solid rgba(150,130,210,.45);font-size:11px;color:var(--ink);font-weight:500;box-shadow:inset 0 1px 0 #fff}
.pill svg{width:14px;height:14px;stroke:var(--purple);fill:none;stroke-width:1.6}
.hero h1{font-family:'Cormorant Garamond',serif;font-weight:500;font-size:60px;line-height:1.02;margin-top:16px;color:var(--ink)}
.hero h1 em{font-style:normal;background:linear-gradient(90deg,#a98ae0,#8d7ad8);-webkit-background-clip:text;background-clip:text;color:transparent}
.hero p{margin-top:16px;font-size:13.2px;line-height:1.55}
.hero .cta{display:flex;gap:16px;margin-top:20px}
.hero .cta .btn{height:36px}
.hero .cta .btn-fill{padding:0 24px}
.hero .cta .btn-out{padding:0 22px}

/* KARTU KACA */
.card{position:relative;overflow:hidden;border-radius:20px;background:var(--glassbg);border:var(--glassbd);box-shadow:var(--glasssh);
  backdrop-filter:blur(24px) saturate(1.7);-webkit-backdrop-filter:blur(24px) saturate(1.7);transition:transform .35s,box-shadow .35s;animation:rise .8s both}
.card::before{content:"";position:absolute;inset:0;border-radius:inherit;pointer-events:none;z-index:0;
  background:linear-gradient(115deg,rgba(255,255,255,.6) 0%,rgba(255,255,255,0) 30%,rgba(255,255,255,0) 68%,rgba(255,255,255,.3) 100%)}
.card:hover{transform:translateY(-4px);box-shadow:0 22px 56px rgba(130,110,205,.26),inset 0 1.5px 0 rgba(255,255,255,.95),inset 0 -1px 0 rgba(170,150,230,.2)}
.card>.hd,.card>.car,.card>.cats,.card>.stats,.card>.revs{position:relative;z-index:1}
.row1{display:grid;grid-template-columns:710fr 380fr 340fr;gap:12px;margin-top:16px}
.row2{display:grid;grid-template-columns:682fr 385fr 383fr;gap:14px;margin-top:16px}
.row1>.card:nth-child(2){animation-delay:.1s}.row1>.card:nth-child(3){animation-delay:.2s}
.row2>.card:nth-child(1){animation-delay:.1s}.row2>.card:nth-child(2){animation-delay:.2s}.row2>.card:nth-child(3){animation-delay:.3s}
.hd{display:flex;align-items:flex-start;gap:10px;padding:16px 20px 0 18px}
.hd .ic{width:34px;height:34px;flex-shrink:0}
.hd .ic svg{width:100%;height:100%;stroke:var(--purple);fill:rgba(190,160,235,.28);stroke-width:1.4}
.hd h2{font-family:'Cormorant Garamond',serif;font-weight:600;font-size:22px;line-height:1.1}
.hd p{font-size:10.5px;color:var(--ink2);margin-top:3px}
.hd .all{margin-left:auto;font-size:9.5px;margin-top:6px;white-space:nowrap;transition:.2s}
.hd .all:hover{color:var(--purple)}

/* item kaca kecil */
.glass-i{background:linear-gradient(150deg,rgba(255,255,255,.88),rgba(255,255,255,.5));border:1px solid rgba(255,255,255,.9);box-shadow:0 6px 18px rgba(130,110,205,.12),inset 0 1px 0 #fff;position:relative;overflow:hidden;transition:.3s}
.glass-i::after{content:"";position:absolute;top:0;left:-70%;width:45%;height:100%;background:linear-gradient(100deg,transparent,rgba(255,255,255,.7),transparent);transform:skewX(-20deg);transition:left .7s;pointer-events:none}
.glass-i:hover{transform:translateY(-4px);box-shadow:0 14px 28px rgba(130,110,205,.24),inset 0 1px 0 #fff}
.glass-i:hover::after{left:140%}

/* BUKU */
.car{position:relative;padding:12px 40px 18px 40px}
.track{display:flex;gap:12px;overflow-x:auto;scroll-behavior:smooth;scrollbar-width:none;padding:6px 2px 10px}
.track::-webkit-scrollbar{display:none}
.book{flex:0 0 138px;height:198px;border-radius:14px;padding:11px 11px 0;display:block}
.cover{position:relative;width:80px;height:112px;margin:0 auto;border-radius:4px;overflow:hidden;box-shadow:0 8px 14px rgba(60,50,110,.3);background:linear-gradient(160deg,#6c5bb5,#b693e4);display:flex;align-items:center;justify-content:center;text-align:center;color:#fff;font:600 9px/1.2 'Poppins';padding:6px}
.cover img{width:100%;height:100%;object-fit:cover;display:block}
.cover::after{content:"";position:absolute;inset:0;background:linear-gradient(110deg,rgba(255,255,255,.35),transparent 40%)}
.bm{position:absolute;right:9px;top:9px;width:14px;height:14px;stroke:#fff;fill:none;stroke-width:1.8;filter:drop-shadow(0 1px 2px rgba(0,0,0,.4));z-index:2}
.book h4{font-size:10.5px;font-weight:500;margin-top:9px;line-height:1.25;height:26px;overflow:hidden}
.book .au{font-size:9px;color:var(--muted);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.book .rt{font-size:9.5px;color:var(--ink2);margin-top:5px;display:flex;align-items:center;gap:3px}
.chip{display:inline-block;margin-top:6px;padding:2px 9px;border-radius:6px;background:rgba(170,150,230,.22);color:#6a5fb0;font-size:8.5px}
.arrow{position:absolute;top:50%;transform:translateY(-30%);width:30px;height:30px;border-radius:50%;background:rgba(255,255,255,.92);border:1px solid rgba(150,130,210,.45);box-shadow:0 6px 14px rgba(140,120,200,.28);display:flex;align-items:center;justify-content:center;cursor:pointer;z-index:3;transition:.2s}
.arrow:hover{background:var(--grad)}.arrow:hover svg{stroke:#fff}
.arrow svg{width:12px;height:12px;stroke:var(--purple);fill:none;stroke-width:2.4}
.arrow.l{left:6px}.arrow.r{right:6px}
.empty{padding:34px 24px;font-size:12px;color:var(--ink2);text-align:center;width:100%}

/* KATEGORI */
.cats{display:grid;grid-template-columns:repeat(4,1fr);gap:11px;padding:16px 20px 20px}
.cat{height:84px;border-radius:14px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;font-size:9.5px;color:var(--ink2);text-align:center;padding:0 4px}
.cat svg{width:26px;height:26px;fill:none;stroke-width:1.6;stroke-linecap:round;stroke-linejoin:round;position:relative;z-index:1}
.c-blue{background:linear-gradient(150deg,#eef3fd,#dbe5fa)}.c-blue svg{stroke:#6b8fd6}
.c-pink{background:linear-gradient(150deg,#fdeef4,#f9dbe8)}.c-pink svg{stroke:#e38aab}
.c-lilac{background:linear-gradient(150deg,#f1ecfc,#e1d9f8)}.c-lilac svg{stroke:#9a82d6}
.c-green{background:linear-gradient(150deg,#e9f7f0,#d5eee2)}.c-green svg{stroke:#63b58b}
.c-peach{background:linear-gradient(150deg,#fff2e6,#fbe2cb)}.c-peach svg{stroke:#e9a15a}

/* STATISTIK */
.stats{display:grid;grid-template-columns:1fr 1fr;gap:12px;padding:14px 18px 20px}
.stat{height:74px;border-radius:14px;display:flex;align-items:center;gap:12px;padding:0 12px}
.stat .si{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:inset 0 1px 0 rgba(255,255,255,.9)}
.stat .si svg{width:20px;height:20px;fill:none;stroke-width:1.6;stroke-linecap:round;stroke-linejoin:round}
.stat b{display:block;font-family:'Cormorant Garamond',serif;font-size:22px;font-weight:600;line-height:1}
.stat small{display:block;font-size:9.5px;color:var(--ink2);margin-top:3px}

/* ULASAN */
.revs{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;padding:14px 18px 18px}
.rev{min-height:128px;border-radius:14px;padding:12px 14px}
.rev .u{display:flex;align-items:center;gap:10px}
.av{width:38px;height:38px;border-radius:50%;background:var(--grad);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:14px;box-shadow:inset 0 1px 0 rgba(255,255,255,.6)}
.rev .u b{font-size:10.5px;font-weight:600;display:block}
.rev p{font-size:9.6px;line-height:1.5;color:var(--ink2);margin-top:9px}
.rev .st{margin-top:8px}

/* PROMO */
.promo{min-height:184px;padding:22px}
.promo .art{position:absolute;inset:0;background-size:cover;background-position:right center;z-index:0}
.promo.ch{background:linear-gradient(120deg,rgba(236,224,250,.9),rgba(230,224,250,.5))}
.promo.lu{background:linear-gradient(120deg,rgba(226,232,253,.92),rgba(238,230,252,.55) 60%,rgba(214,236,250,.45))}
.promo>.pc{position:relative;z-index:2}
.promo h3{font-family:'Cormorant Garamond',serif;font-size:25px;font-weight:600;font-style:italic;margin-top:20px}
.promo p{font-size:10.5px;margin-top:10px;max-width:230px;line-height:1.5}
.promo .btn{height:32px;padding:0 20px;margin-top:20px;font-size:11px}
.lu h3{font-style:normal;margin-top:0}
.lu .sub{font-size:10px;color:var(--ink2);margin-top:2px}
.lu p{max-width:190px;margin-top:14px}
.mascot{position:absolute;right:10px;bottom:2px;height:128px;z-index:2;animation:floaty 4s ease-in-out infinite;filter:drop-shadow(0 10px 14px rgba(120,100,200,.3))}
.bubble{position:absolute;right:20px;top:14px;z-index:3;width:96px;padding:8px 10px;border-radius:14px;
  background:rgba(255,255,255,.8);backdrop-filter:blur(8px);border:1px solid rgba(255,255,255,.95);font-size:8px;line-height:1.4;text-align:center;box-shadow:0 6px 16px rgba(140,120,200,.22)}

/* FOOTER */
.foot{position:relative;margin-top:18px;overflow:hidden;background:linear-gradient(180deg,#f4eefb,#e9dff8);border-top:1px solid rgba(255,255,255,.9)}
.foot .art{position:absolute;inset:0;background-size:cover;background-position:center}
.foot::before{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(255,255,255,.5),rgba(255,255,255,.1));z-index:1}
.foot-in{position:relative;z-index:2;max-width:1536px;margin:0 auto;padding:0 24px;height:144px;display:grid;grid-template-columns:1.35fr 1.1fr .9fr .6fr 1.1fr;align-items:center}
.fl{display:flex;align-items:center;gap:14px;padding-left:150px}
.fl img{width:56px;height:60px;object-fit:contain;flex-shrink:0}
.fl b{font-family:'Cormorant Garamond',serif;font-weight:500;font-size:23px;display:block;white-space:nowrap}
.fl small{font-size:8.5px;color:var(--purple);display:block;white-space:nowrap;margin-top:2px}
.fc{border-left:1px solid rgba(150,140,210,.3);padding-left:30px;height:80px;font-size:9.5px;color:var(--ink2)}
.fc h5{font-size:10.5px;font-weight:600;color:var(--ink);margin-bottom:10px}
.fc li{list-style:none;display:flex;align-items:center;gap:9px;margin-bottom:8px}
.fc li a:hover{color:var(--purple)}
.fc li svg{width:12px;height:12px;stroke:var(--ink2);fill:none;stroke-width:1.8;flex-shrink:0}
.fc.nb{border-left:0}
.soc{display:flex;gap:16px}
.soc svg{width:15px;height:15px;stroke:var(--ink);fill:none;stroke-width:1.7;transition:.2s}
.soc svg:hover{stroke:var(--purple);transform:translateY(-2px)}
.quote{font-family:'Cormorant Garamond',serif;font-style:italic;font-size:13px;line-height:1.55;text-align:center}
.quote span{display:block;font-size:11px;margin-top:6px}

/* RESPONSIVE */
@media (max-width:1100px){
  .row1,.row2{grid-template-columns:1fr}
  .menu,.search{display:none}
  .nav .btn-out,.nav .btn-fill{display:none}
  .burger{display:block}
  .menu.open{display:flex;position:absolute;top:66px;left:0;right:0;flex-direction:column;gap:6px;margin:0;padding:14px 22px;border-radius:22px;background:rgba(255,255,255,.92);backdrop-filter:blur(20px);box-shadow:0 10px 30px rgba(140,120,200,.25)}
  .hero-in{padding-left:28px}.hero h1{font-size:46px}
  .foot-in{grid-template-columns:1fr;height:auto;padding:26px 24px;gap:18px}
  .fl{padding-left:0}.fc{border-left:0;padding-left:0;height:auto}
}
@media (max-width:640px){
  .nav{width:calc(100% - 24px);padding:0 16px}
  .hero{height:auto;padding-bottom:36px}.hero h1{font-size:38px}
  .cats{grid-template-columns:repeat(2,1fr)}.revs{grid-template-columns:1fr}.wrap{padding:0 12px}
}

/* ===== V2-17 : buku 3D, glow, parallax ===== */
.hero .art{transform:translate(calc(var(--mx,0) * -10px),calc(var(--my,0) * -6px)) scale(1.05);transition:transform .25s ease-out}
.floatlayer{position:absolute;right:0;top:0;width:760px;height:392px;z-index:3;perspective:1000px;pointer-events:none;
  transform:translate(calc(var(--mx,0) * 18px),calc(var(--my,0) * 11px));transition:transform .25s ease-out}
.glowpulse{position:absolute;left:44px;top:206px;width:330px;height:230px;mix-blend-mode:screen;pointer-events:none;
  background:radial-gradient(ellipse at 50% 55%,rgba(255,255,255,.95),rgba(255,214,244,.5) 38%,transparent 70%);animation:gpulse 4s ease-in-out infinite}
.rays{position:absolute;left:34px;top:40px;width:350px;height:330px;mix-blend-mode:screen;pointer-events:none;opacity:.55;
  background:repeating-conic-gradient(from -30deg at 50% 100%,rgba(255,255,255,.65) 0 3deg,transparent 3deg 11deg);
  -webkit-mask-image:radial-gradient(ellipse at 50% 100%,#000 0,transparent 72%);mask-image:radial-gradient(ellipse at 50% 100%,#000 0,transparent 72%);
  animation:sway 9s ease-in-out infinite alternate;transform-origin:50% 100%}
@keyframes gpulse{0%,100%{opacity:.6;transform:scale(.96)}50%{opacity:1;transform:scale(1.06)}}
@keyframes sway{from{transform:rotate(-5deg)}to{transform:rotate(5deg)}}
@keyframes bkf{
  0%,100%{transform:translateY(0) rotateY(var(--ry)) rotateX(var(--rx)) rotateZ(var(--rz))}
  50%{transform:translateY(-16px) rotateY(calc(var(--ry) + 16deg)) rotateX(calc(var(--rx) - 5deg)) rotateZ(calc(var(--rz) + 3deg))}}
@keyframes halo{0%,100%{opacity:.55;transform:translateZ(-30px) scale(.92)}50%{opacity:1;transform:translateZ(-30px) scale(1.1)}}

.bk{--w:76px;--h:108px;--d:14px;--ry:-24deg;--rx:8deg;--rz:8deg;position:absolute;width:var(--w);height:var(--h);display:block;pointer-events:auto;cursor:pointer;
  transform-style:preserve-3d;animation:bkf 6.5s ease-in-out infinite;transition:filter .3s}
.bk::before{content:"";position:absolute;inset:-34px;border-radius:50%;pointer-events:none;
  background:radial-gradient(circle,rgba(255,255,255,.85),rgba(236,214,255,.45) 45%,transparent 70%);transform:translateZ(-30px);animation:halo 3.6s ease-in-out infinite}
.bk:hover{animation-play-state:paused}
.bk .f{position:absolute;backface-visibility:hidden}
.bk .front{inset:0;transform:translateZ(calc(var(--d) / 2));border-radius:2px 5px 5px 2px;overflow:hidden;
  background:linear-gradient(160deg,#6c5bb5,#b693e4);box-shadow:0 0 0 1px rgba(255,255,255,.45),0 0 26px rgba(255,255,255,.7),0 16px 26px rgba(70,55,140,.4);
  display:flex;align-items:center;justify-content:center;text-align:center;color:#fff;padding:6px}
.bk .front img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.bk .front em{position:relative;font:600 9px/1.25 'Poppins',sans-serif;font-style:normal;text-shadow:0 1px 3px rgba(0,0,0,.4)}
.bk .front::after{content:"";position:absolute;inset:0;background:linear-gradient(115deg,rgba(255,255,255,.55) 0%,rgba(255,255,255,.08) 35%,transparent 55%,rgba(0,0,0,.18) 100%)}
.bk .front::before{content:"";position:absolute;left:0;top:0;bottom:0;width:7px;z-index:2;background:linear-gradient(90deg,rgba(0,0,0,.28),transparent)}
.bk .back{inset:0;transform:translateZ(calc(var(--d) / -2)) rotateY(180deg);background:#6d5cb8}
.bk .spine{top:0;bottom:0;width:var(--d);left:calc(50% - var(--d) / 2);transform:rotateY(-90deg) translateZ(calc(var(--w) / 2));background:linear-gradient(180deg,#8b78d6,#4b3d99)}
.bk .pages{top:2px;bottom:2px;width:var(--d);left:calc(50% - var(--d) / 2);transform:rotateY(90deg) translateZ(calc(var(--w) / 2));
  background:repeating-linear-gradient(90deg,#fffdf4 0 1.5px,#e6dcc6 1.5px 3px)}
.bk .topf{left:2px;right:2px;height:var(--d);top:calc(50% - var(--d) / 2);transform:rotateX(90deg) translateZ(calc(var(--h) / 2));
  background:repeating-linear-gradient(0deg,#fffdf4 0 1.5px,#e6dcc6 1.5px 3px)}
.bk .orn{position:absolute;left:50%;top:38%;width:30px;height:30px;margin-left:-15px;border-radius:50%;border:1.5px solid rgba(255,255,255,.75);box-shadow:0 0 12px rgba(255,255,255,.6)}
.bk .orn::after{content:"";position:absolute;left:50%;top:50%;width:14px;height:14px;margin:-7px 0 0 -7px;background:rgba(255,255,255,.7);transform:rotate(45deg)}

.bk1{left:176px;top:30px;--ry:-30deg;--rz:12deg}
.bk2{left:304px;top:92px;--ry:-18deg;--rz:-8deg;animation-delay:-1.6s}
.bk3{left:396px;top:196px;--w:68px;--h:98px;--ry:-26deg;--rz:-14deg;animation-delay:-3.2s}
.bk4{left:66px;top:108px;--ry:-34deg;--rz:-18deg;animation-delay:-4.8s}
.bk5{left:240px;top:196px;--w:62px;--h:90px;--ry:-20deg;--rz:6deg;animation-delay:-2.2s}
.bk1 .front.ph{background:linear-gradient(160deg,#2d3a7a,#5a6bd0)}
.bk2 .front.ph{background:linear-gradient(160deg,#f6ecd2,#e3d2a2)}
.bk3 .front.ph{background:linear-gradient(160deg,#f3b9cf,#e08cb2)}
.bk4 .front.ph{background:linear-gradient(160deg,#7b4b3a,#b07a5c)}
.bk5 .front.ph{background:linear-gradient(160deg,#9fd3c0,#5fae94)}

@media (max-width:1100px){.floatlayer{width:560px;opacity:.9}.bk4,.bk5{display:none}}
@media (max-width:760px){.floatlayer{display:none}}
@media (prefers-reduced-motion:reduce){.bk,.bk::before,.glowpulse,.rays,.bgfx i,.hero .tw{animation:none}}

/* ===== V2-18 : kastil megah 3D + kilatan ===== */
.castlelayer{position:absolute;right:0;top:0;width:760px;height:392px;z-index:2;pointer-events:none;
  transform:perspective(1400px) translate(calc(var(--mx,0) * 12px),calc(var(--my,0) * 7px)) rotateY(calc(var(--mx,0) * -4deg)) rotateX(calc(var(--my,0) * 2.5deg));
  transition:transform .25s ease-out}
.castlelayer .cimg{position:absolute;inset:0;width:100%;height:100%;object-fit:contain;object-position:right bottom;
  filter:drop-shadow(0 22px 24px rgba(80,60,160,.4)) drop-shadow(0 0 28px rgba(255,255,255,.6));animation:cbreath 7s ease-in-out infinite}
.castlelayer .cglow{position:absolute;left:170px;top:20px;width:620px;height:340px;mix-blend-mode:screen;
  background:radial-gradient(ellipse at 50% 55%,rgba(255,244,214,.9),rgba(255,214,244,.45) 40%,transparent 70%);animation:gpulse 5s ease-in-out infinite}
.castlelayer .cshine{position:absolute;inset:0;mix-blend-mode:screen;
  -webkit-mask-repeat:no-repeat;mask-repeat:no-repeat;-webkit-mask-size:100% 100%;mask-size:100% 100%;
  background:linear-gradient(105deg,transparent 36%,rgba(255,255,255,0) 42%,rgba(255,255,255,.95) 49%,rgba(255,243,196,.95) 51%,rgba(255,255,255,0) 58%,transparent 64%);
  background-size:320% 100%;animation:cshine 5.6s ease-in-out infinite}
.castlelayer .gl{position:absolute;width:28px;height:28px;margin:-14px 0 0 -14px;animation:twinkle 2.8s ease-in-out infinite;
  background:radial-gradient(circle,#fff 0 3px,rgba(255,255,255,0) 8px),linear-gradient(#fff,#fff) 50% 50%/1.6px 100% no-repeat,linear-gradient(#fff,#fff) 50% 50%/100% 1.6px no-repeat;
  filter:drop-shadow(0 0 6px #fff) drop-shadow(0 0 14px #ffe9a8)}
@keyframes cbreath{0%,100%{transform:translateY(0) scale(1)}50%{transform:translateY(-3px) scale(1.008)}}
@keyframes cshine{0%{background-position:100% 0}60%,100%{background-position:0% 0}}
@media (max-width:1100px){.castlelayer{width:560px}}
@media (prefers-reduced-motion:reduce){.castlelayer .cimg,.castlelayer .cglow,.castlelayer .cshine,.castlelayer .gl{animation:none}}

/* ===== V2-19 : kastil dari gambar watercolor ===== */
.castlelayer.raster{top:60px;height:332px}
.castlelayer.raster .cimg{object-position:85% 100%;filter:drop-shadow(0 20px 22px rgba(90,60,150,.36)) drop-shadow(0 0 22px rgba(255,255,255,.55)) saturate(1.12) contrast(1.04)}
.castlelayer.raster .cshine{-webkit-mask-size:contain;mask-size:contain;-webkit-mask-position:85% 100%;mask-position:85% 100%}
.castlelayer.raster .cglow{top:10px;height:300px}
</style>
    <link rel="stylesheet" href="{{ asset('css/landing-polish.css') }}?v={{ filemtime(public_path('css/landing-polish.css')) }}">
    <script src="{{ asset('js/landing-polish.js') }}?v={{ filemtime(public_path('js/landing-polish.js')) }}" defer></script>
    <!-- trafie-icon -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg?v=4">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32.png?v=4">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=4">
    <link rel="shortcut icon" href="/favicon.ico?v=4">
    <meta name="theme-color" content="#a78bfa">
    <!-- /trafie-icon -->
<style>
/* rev-glam */
@keyframes revtw{0%,100%{filter:drop-shadow(0 0 2px rgba(240,169,59,.4))}50%{filter:drop-shadow(0 0 7px rgba(240,169,59,.95))}}
#reviews .revs{display:grid !important;grid-template-columns:repeat(auto-fill,minmax(230px,1fr)) !important;gap:1rem !important;align-items:stretch}
#reviews .revs .rev{position:relative;overflow:hidden;display:flex;flex-direction:column;gap:.7rem;padding:1.15rem 1.2rem 1rem !important;border-radius:22px !important;
 background:linear-gradient(150deg,rgba(255,255,255,.92),rgba(247,238,255,.6)) !important;
 border:1px solid rgba(255,255,255,.9) !important;
 box-shadow:inset 0 1px 0 #fff,0 16px 36px -16px rgba(124,92,255,.5),0 0 30px -12px rgba(255,111,181,.45) !important;
 backdrop-filter:blur(16px) saturate(160%);-webkit-backdrop-filter:blur(16px) saturate(160%);
 transition:transform .3s ease,box-shadow .3s ease}
#reviews .revs .rev:hover{transform:translateY(-6px);box-shadow:inset 0 1px 0 #fff,0 24px 44px -14px rgba(124,92,255,.6),0 0 38px -8px rgba(255,111,181,.6) !important}
#reviews .revs .rev::before{content:"\201D";position:absolute;right:.9rem;top:-.35rem;font:700 5rem/1 Georgia,serif;
 background:linear-gradient(135deg,#b9a8ee,#f4a6c8);-webkit-background-clip:text;background-clip:text;color:transparent;opacity:.45;pointer-events:none}
#reviews .revs .rev::after{content:"";position:absolute;left:0;top:0;width:100%;height:4px;background:linear-gradient(90deg,#9a80ee,#c58ae8,#f29bc8)}
#reviews .revs .rev .u{display:flex;align-items:center;gap:.65rem;position:relative;z-index:1}
#reviews .revs .rev .av{flex:none;width:42px !important;height:42px !important;border-radius:50% !important;display:grid;place-items:center;font-weight:700;color:#fff !important;
 background:linear-gradient(135deg,#9a80ee,#f29bc8) !important;box-shadow:0 0 0 3px rgba(255,255,255,.9),0 8px 18px -6px rgba(154,128,238,.8)}
#reviews .revs .rev .u b{font-size:.9rem;color:#2d2a4a}
#reviews .revs .rev p{margin:0;flex:1;font-family:'Cormorant Garamond','Playfair Display',Georgia,serif;font-style:italic;font-size:1.08rem !important;line-height:1.5 !important;color:#3b3858 !important;text-transform:none !important;position:relative;z-index:1;word-break:break-word}
#reviews .revs .rev .st{display:flex;gap:2px;padding-top:.6rem;border-top:1px dashed rgba(154,128,238,.35);animation:revtw 2.8s ease-in-out infinite}
#reviews .revs .rev .st svg{width:17px;height:17px}
@media(prefers-reduced-motion:reduce){#reviews .revs .rev .st{animation:none}}
</style>
</head>
<body>
<div class="bgfx"><i></i><i></i><i></i><i></i></div>

{{-- NAV + HERO --}}
<section class="hero" id="home">
  @if($img('hero'))<div class="art" style="background-image:url('{{ $img('hero') }}')"></div>@endif
  <div class="fade"></div>
  <svg class="tw" style="left:44%;top:120px;animation-delay:.4s" width="16" height="16" viewBox="-12 -12 24 24"><path d="M0-12Q1.5-1.5 12 0Q1.5 1.5 0 12Q-1.5 1.5-12 0Q-1.5-1.5 0-12Z" fill="#fff"/></svg>
  <svg class="tw" style="left:52%;top:300px;animation-delay:1.2s" width="12" height="12" viewBox="-12 -12 24 24"><path d="M0-12Q1.5-1.5 12 0Q1.5 1.5 0 12Q-1.5 1.5-12 0Q-1.5-1.5 0-12Z" fill="#fff"/></svg>
  <svg class="tw" style="left:63%;top:90px;animation-delay:2s" width="14" height="14" viewBox="-12 -12 24 24"><path d="M0-12Q1.5-1.5 12 0Q1.5 1.5 0 12Q-1.5 1.5-12 0Q-1.5-1.5 0-12Z" fill="#fff"/></svg>
  <svg class="tw" style="left:78%;top:250px;animation-delay:.8s" width="12" height="12" viewBox="-12 -12 24 24"><path d="M0-12Q1.5-1.5 12 0Q1.5 1.5 0 12Q-1.5 1.5-12 0Q-1.5-1.5 0-12Z" fill="#fff"/></svg>

  <nav class="nav">
    <a href="{{ url('/') }}" class="brand">
      @if($logoUrl)<img src="{{ $logoUrl }}" alt="Trafie-library">@endif
      <span><b>Trafie-library</b><small>Where Every Book Blooms Into a Universe</small></span>
    </a>
    <div class="menu" id="menu">
      <a href="#home" class="on">Home</a>
      <a href="#explore">Explore</a>
      <a href="#categories">Categories</a>
      <a href="#reviews">Reviews</a>
      <a href="#about">About</a>
    </div>
    <form class="search" action="{{ $catalog }}" method="get">
      <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4"/></svg>
      <input type="text" name="q" placeholder="Cari buku, penulis, atau kategori...">
    </form>
    @if($loggedIn)
      <a href="{{ $dashboard }}" class="btn btn-fill">Dashboard</a>
    @else
      <a href="{{ $loginUrl }}" class="btn btn-out">Login</a>
      <a href="{{ $registerUrl }}" class="btn btn-fill">Register</a>
    @endif
    <button class="burger" onclick="document.getElementById('menu').classList.toggle('open')" aria-label="Menu">
      <svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
    </button>
  </nav>

  @if($img('castle'))
  <div class="castlelayer raster">
    <div class="cglow"></div>
    <img class="cimg" src="{{ $img('castle') }}" alt="">
    <div class="cshine" style="-webkit-mask-image:url('{{ $img('castle') }}');mask-image:url('{{ $img('castle') }}')"></div>
    <i class="gl" style="left:517.8px;top:5.3px;animation-delay:0s"></i>
    <i class="gl" style="left:474.9px;top:78.4px;animation-delay:0.7s"></i>
    <i class="gl" style="left:567.9px;top:64.7px;animation-delay:1.4s"></i>
    <i class="gl" style="left:399.7px;top:147.4px;animation-delay:2.1s"></i>
    <i class="gl" style="left:630.7px;top:161.4px;animation-delay:0.3s"></i>
    <i class="gl" style="left:465.8px;top:151.4px;animation-delay:1.1s"></i>
    <i class="gl" style="left:591.1px;top:172.6px;animation-delay:1.8s"></i>
    <i class="gl" style="left:516.3px;top:233.1px;animation-delay:2.4s"></i>
  </div>
  @endif
  <div class="floatlayer" aria-hidden="false">
    <div class="rays"></div>
    <div class="glowpulse"></div>
    @php
      $fb = collect($books)->filter(fn($b) => !empty($b['cover']))->take(5)->values();
      if ($fb->count() < 5) { $fb = collect($books)->take(5)->values(); }
      $isPh = $fb->isEmpty();
      if ($isPh) { $fb = collect(range(1, 5))->map(fn($i) => ['title' => '', 'cover' => null]); }
    @endphp
    @foreach($fb as $i => $b)
      <a href="{{ $catalog }}" class="bk bk{{ $i + 1 }}" title="{{ $b['title'] }}">
        <span class="f front {{ $isPh ? 'ph' : '' }}">
          @if(!empty($b['cover']))<img src="{{ $b['cover'] }}" alt="{{ $b['title'] }}">
          @elseif($b['title'] !== '')<em>{{ $b['title'] }}</em>
          @else<i class="orn"></i>@endif
        </span>
        <span class="f back"></span><span class="f spine"></span><span class="f pages"></span><span class="f topf"></span>
      </a>
    @endforeach
  </div>

  <div class="hero-in">
    <span class="pill"><svg viewBox="0 0 24 24"><path d="M12 3l2.4 5.6L20 9l-4.4 3.8L17 19l-5-3-5 3 1.4-6.2L4 9l5.6-.4z"/></svg>Trafie-library</span>
    <h1>Discover Your Next<br>Favorite <em>Universe</em></h1>
    <p>Every book opens a new universe.<br>Find your next adventure in Trafie-library.</p>
    <div class="cta">
      <a href="{{ $catalog }}" class="btn btn-fill">Explore Books &nbsp;&rarr;</a>
      <a href="{{ $loggedIn ? $dashboard : $registerUrl }}" class="btn btn-out">{{ $loggedIn ? 'Open Dashboard' : 'Join the Library' }}</a>
    </div>
  </div>
</section>

<div class="wrap">
  <section class="row1">
    <div class="card" id="explore">
      <div class="hd">
        <div class="ic"><svg viewBox="0 0 24 24"><path d="M12 3l2.4 5.6L20 9l-4.4 3.8L17 19l-5-3-5 3 1.4-6.2L4 9l5.6-.4z"/></svg></div>
        <div><h2>Buku Pilihan</h2><p>Buku-buku terbaik yang sedang populer di kalangan pembaca.</p></div>
        <a class="all" href="{{ $catalog }}">Lihat Semua &rarr;</a>
      </div>
      <div class="car">
        <div class="arrow l" onclick="document.getElementById('trk').scrollBy({left:-300})"><svg viewBox="0 0 24 24"><path d="M15 5l-7 7 7 7"/></svg></div>
        <div class="track" id="trk">
          @forelse($books as $b)
            <a href="{{ $catalog }}" class="book glass-i">
              <div class="cover">
                @if($b['cover'])<img src="{{ $b['cover'] }}" alt="{{ $b['title'] }}" loading="lazy">@else{{ $b['title'] }}@endif
              </div>
              <svg class="bm" viewBox="0 0 24 24"><path d="M6 3h12v18l-6-4-6 4z"/></svg>
              <h4>{{ $b['title'] }}</h4>
              <div class="au">{{ $b['author'] }}</div>
              <div class="rt">
                @if($b['rating']){!! $star !!}{{ $b['rating']['avg'] }} ({{ $b['rating']['n'] }})@else<span style="color:var(--muted)">Belum ada ulasan</span>@endif
              </div>
              @if($b['category'])<span class="chip">{{ $b['category'] }}</span>@endif
            </a>
          @empty
            <div class="empty">Belum ada buku di katalog. Tambahkan buku lewat panel admin dan akan tampil di sini.</div>
          @endforelse
        </div>
        <div class="arrow r" onclick="document.getElementById('trk').scrollBy({left:300})"><svg viewBox="0 0 24 24"><path d="M9 5l7 7-7 7"/></svg></div>
      </div>
    </div>

    <div class="card" id="categories">
      <div class="hd">
        <div class="ic"><svg viewBox="0 0 24 24"><path d="M12 3l2.4 5.6L20 9l-4.4 3.8L17 19l-5-3-5 3 1.4-6.2L4 9l5.6-.4z"/></svg></div>
        <div><h2>Kategori Populer</h2><p>Jelajahi berbagai kategori buku favoritmu.</p></div>
        <a class="all" href="{{ $catalog }}">Lihat Semua &rarr;</a>
      </div>
      <div class="cats">
        @foreach($cats as $c)
          <a class="cat glass-i {{ $c[1] }}" href="{{ $catalog }}?q={{ urlencode($c[0]) }}">
            <svg viewBox="0 0 24 24">{!! $c[2] !!}</svg><span style="position:relative;z-index:1">{{ $c[0] }}</span>
          </a>
        @endforeach
      </div>
    </div>

    <div class="card">
      <div class="hd">
        <div class="ic"><svg viewBox="0 0 24 24"><path d="M12 3l2.4 5.6L20 9l-4.4 3.8L17 19l-5-3-5 3 1.4-6.2L4 9l5.6-.4z"/></svg></div>
        <div><h2>Statistik Perpustakaan</h2><p>Bersama kita membangun budaya membaca.</p></div>
      </div>
      <div class="stats">
        <div class="stat glass-i"><div class="si" style="background:linear-gradient(150deg,#f8ecfb,#ebd3f3)"><svg viewBox="0 0 24 24" style="stroke:#b57ad0"><path d="M4 6h7a2 2 0 0 1 2 2v11a2 2 0 0 0-2-2H4zM20 6h-7v11h7z"/></svg></div><div><b>{{ $fmt($stats['books']) }}</b><small>Total Buku</small></div></div>
        <div class="stat glass-i"><div class="si" style="background:linear-gradient(150deg,#e9f8f0,#cfeadb)"><svg viewBox="0 0 24 24" style="stroke:#5fb48a"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg></div><div><b>{{ $fmt($stats['students']) }}</b><small>Siswa Aktif</small></div></div>
        <div class="stat glass-i"><div class="si" style="background:linear-gradient(150deg,#e9effd,#d1dcf8)"><svg viewBox="0 0 24 24" style="stroke:#6f8ad6"><path d="M4 6h7a2 2 0 0 1 2 2v11a2 2 0 0 0-2-2H4zM20 6h-7v11h7z"/></svg></div><div><b>{{ $fmt($stats['loans']) }}</b><small>Peminjaman Bulanan</small></div></div>
        <div class="stat glass-i"><div class="si" style="background:linear-gradient(150deg,#e9f8f0,#cfeadb)"><svg viewBox="0 0 24 24" style="stroke:#5fb48a"><path d="M12 3l2.4 5.6L20 9l-4.4 3.8L17 19l-5-3-5 3 1.4-6.2L4 9l5.6-.4z"/></svg></div><div><b>{{ $stats['satisfaction'] !== null ? $stats['satisfaction'].'%' : '-' }}</b><small>Tingkat Kepuasan</small></div></div>
      </div>
    </div>
  </section>

  <section class="row2">
    <div class="card" id="reviews">
      <div class="hd">
        <div class="ic"><svg viewBox="0 0 24 24"><path d="M12 3l2.4 5.6L20 9l-4.4 3.8L17 19l-5-3-5 3 1.4-6.2L4 9l5.6-.4z"/></svg></div>
        <div><h2>Apa Kata Mereka?</h2><p>Pendapat para pembaca tentang Trafie-library.</p></div>
      </div>
      <div class="revs">
        @forelse($reviews as $r)
          <div class="rev glass-i">
            <div class="u"><div class="av">{{ strtoupper(mb_substr($r['name'], 0, 1)) }}</div><div><b>{{ $r['name'] }}</b></div></div>
            <p>&ldquo;{{ \Illuminate\Support\Str::limit($r['text'], 130) }}&rdquo;</p>
            <div class="st">@for($i = 0; $i < max(0, min(5, $r['rating'])); $i++){!! $star !!}@endfor</div>
          </div>
        @empty
          <div class="empty" style="grid-column:1/-1">Belum ada ulasan. Siswa yang pernah meminjam buku bisa memberi rating dan ulasan di halaman detail buku, dan langsung tampil di sini.</div>
        @endforelse
      </div>
    </div>

    <div class="card promo ch">
      @if($img('challenge'))<div class="art" style="background-image:url('{{ $img('challenge') }}')"></div>@endif
      <div class="pc">
        <h3>Reading Challenge</h3>
        <p>Tantangan membaca untuk versi terbaik dirimu.</p>
        <a href="{{ $catalog }}" class="btn btn-fill">Lihat Challenge &nbsp;&rarr;</a>
      </div>
    </div>

    <div class="card promo lu">
      @if($img('lumi'))<img class="mascot" src="{{ $img('lumi') }}" alt="Lumi">@endif
      <div class="bubble">Hai! Aku Lumi<br>Siap membantu rekomendasi buku untukmu! {!! $spark !!}</div>
      <div class="pc">
        <h3>Lumi AI</h3>
        <div class="sub">Your Reading Companion</div>
        <p>Tanya tentang buku, dapatkan rekomendasi yang sesuai dengan minat dan mood-mu!</p>
        <a href="#" class="btn btn-fill" onclick="alert('Lumi AI segera hadir.');return false;">Chat with Lumi &nbsp;&rarr;</a>
      </div>
    </div>
  </section>
</div>

<footer class="foot" id="about">
  @if($img('footer'))<div class="art" style="background-image:url('{{ $img('footer') }}')"></div>@endif
  <div class="foot-in">
    <div class="fl">
      @if($logoUrl)<img src="{{ $logoUrl }}" alt="">@endif
      <div><b>Trafie-library</b><small>Where Every Book Blooms Into a Universe</small></div>
    </div>
    <div class="fc">
      <h5>Hubungi Kami</h5>
      <ul>
        <li><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/></svg><a href="mailto:andttradika@gmail.com">andttradika@gmail.com</a></li>
        <li><svg viewBox="0 0 24 24"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a2 2 0 0 1-2 2A16 16 0 0 1 3 6a2 2 0 0 1 2-2z"/></svg><a href="tel:+6283836631966">+62 838 3663 1966</a></li>
        <li><svg viewBox="0 0 24 24"><path d="M12 21s-7-6-7-11a7 7 0 0 1 14 0c0 5-7 11-7 11z"/><circle cx="12" cy="10" r="2.5"/></svg>SMK Negeri 1 Ciomas, Bogor</li>
      </ul>
    </div>
    <div class="fc">
      <h5>Tautan Cepat</h5>
      <ul>
        <li><a href="#home">Home</a></li>
        <li><a href="{{ $catalog }}">Explore Books</a></li>
        <li><a href="#categories">Categories</a></li>
        <li><a href="#about">About Us</a></li>
      </ul>
    </div>
    <div class="fc">
      <h5>Ikut Kami</h5>
      <div class="soc">
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".8"/></svg>
        <svg viewBox="0 0 24 24"><path d="M14 3v11a4 4 0 1 1-4-4M14 3c0 3 2 5 5 5"/></svg>
        <svg viewBox="0 0 24 24"><rect x="3" y="6" width="18" height="12" rx="3"/><path d="M10 9.5v5l4-2.5z"/></svg>
        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/></svg>
      </div>
    </div>
    <div class="fc nb">
      <div class="quote">&ldquo;Buku bukan hanya<br>membuka pikiran, tapi juga<br>membuka alam semesta baru.&rdquo;<span>&mdash; Trafie-library &mdash;</span></div>
    </div>
  </div>
</footer>

<script>
(function(){
  var h=document.getElementById('home'); if(!h) return;
  h.addEventListener('mousemove',function(e){
    var r=h.getBoundingClientRect();
    h.style.setProperty('--mx',((e.clientX-r.left)/r.width-.5).toFixed(3));
    h.style.setProperty('--my',((e.clientY-r.top)/r.height-.5).toFixed(3));
  });
  h.addEventListener('mouseleave',function(){ h.style.setProperty('--mx',0); h.style.setProperty('--my',0); });
})();
</script>
@include('partials.gloss')
@include('partials.skin')
@include('partials.statviz')
</body>
</html>