@props(['title', 'sub' => null, 'icon' => 'home'])
@php
    $hp = [
        'home' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/>',
        'coin' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v10M9.5 9.5h4a1.5 1.5 0 0 1 0 3h-3a1.5 1.5 0 0 0 0 3h4"/>',
        'star' => '<path d="M12 2l3 6.5 7 .9-5.1 4.8 1.3 7L12 17.8 5.8 21.2l1.3-7L2 9.4l7-.9z"/>',
        'award' => '<circle cx="12" cy="8" r="6"/><path d="M8.2 13.5L7 22l5-3 5 3-1.2-8.5"/>',
        'cal' => '<rect x="3" y="4" width="18" height="18" rx="3"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        'cog' => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2 12h3M19 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1"/>',
        'user' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'clock' => '<path d="M12 8v4l3 2"/><circle cx="12" cy="12" r="9"/>',
        'mega' => '<path d="M3 11v2a1 1 0 0 0 1 1h2l5 4V6L6 10H4a1 1 0 0 0-1 1z"/><path d="M15 9a4 4 0 0 1 0 6"/>',
        'check' => '<circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/>',
    ];
@endphp
@once
<style>
@keyframes ah-up { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }
.ah { position: relative; overflow: hidden; display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap; margin-bottom: 1.1rem; padding: 1.25rem 1.6rem; border-radius: 26px; font-family: 'Poppins', sans-serif; background: linear-gradient(110deg, rgba(255,255,255,.92), rgba(240,230,253,.85) 55%, rgba(214,230,252,.85)); border: 1px solid #fff; box-shadow: 0 18px 44px rgba(139,92,246,.12); animation: ah-up .5s both; }
.ah::after { content: ''; position: absolute; right: -40px; top: -60px; width: 260px; height: 260px; border-radius: 50%; background: radial-gradient(circle, rgba(247,200,220,.65), transparent 70%); pointer-events: none; }
.ah-l { display: flex; align-items: center; gap: 1rem; position: relative; z-index: 1; }
.ah-ico { flex: 0 0 52px; width: 52px; height: 52px; display: grid; place-items: center; border-radius: 17px; color: #fff; background: linear-gradient(135deg, #a78bfa, #f48fb1); box-shadow: 0 12px 26px rgba(167,125,230,.4), inset 0 1px 0 rgba(255,255,255,.5); }
.ah-ico svg { width: 25px; height: 25px; fill: none; stroke: currentColor; stroke-width: 1.7; stroke-linecap: round; stroke-linejoin: round; }
.ah h1 { margin: 0; font-family: 'Playfair Display', serif; font-size: 1.6rem; color: #2d2a4a; line-height: 1.1; }
.ah p { margin: .2rem 0 0; font-size: .75rem; color: #7b7796; }
.ah-r { position: relative; z-index: 1; display: flex; gap: .6rem; align-items: center; flex-wrap: wrap; }
.ah-btn { display: inline-flex; align-items: center; gap: .4rem; height: 40px; padding: 0 1.2rem; border: 0; border-radius: 13px; cursor: pointer; text-decoration: none; font: 600 .76rem 'Poppins', sans-serif; color: #fff; background: linear-gradient(115deg, #9a80ee, #c58ae8 55%, #f29bc8); box-shadow: 0 10px 22px rgba(160,120,230,.38), inset 0 1px 0 rgba(255,255,255,.5); transition: transform .2s ease; }
.ah-btn:hover { transform: translateY(-2px); }
.ah-btn.soft { color: #5b34a8; background: rgba(255,255,255,.92); border: 1px solid rgba(205,188,245,.95); box-shadow: 0 4px 12px rgba(139,92,246,.1); }
.ah-stats { display: grid; gap: .9rem; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-bottom: 1.1rem; }
.ah-stat { display: flex; align-items: center; gap: .85rem; padding: 1rem 1.1rem; border-radius: 22px; background: rgba(255,255,255,.86); border: 1px solid #fff; box-shadow: 0 10px 26px rgba(139,92,246,.08); }
.ah-stat .i { flex: 0 0 42px; width: 42px; height: 42px; display: grid; place-items: center; border-radius: 15px; font-weight: 700; }
.ah-stat small { display: block; font-size: .66rem; color: #7b7796; }
.ah-stat b { display: block; font-family: 'Playfair Display', serif; font-size: 1.5rem; color: #2d2a4a; line-height: 1.15; }
.ah .x1, .ah-stat .x1 { background: #e6defa; color: #7a54d6; } .ah-stat .x2 { background: #d8f0e2; color: #3f9a6f; } .ah-stat .x3 { background: #fbdbe8; color: #d9558a; } .ah-stat .x4 { background: #d9e9fa; color: #4b8fd6; }
</style>
@endonce
<div class="ah">
    <div class="ah-l">
        <span class="ah-ico"><svg viewBox="0 0 24 24">{!! $hp[$icon] ?? $hp['home'] !!}</svg></span>
        <div><h1>{{ $title }}</h1>@if($sub)<p>{{ $sub }}</p>@endif</div>
    </div>
    <div class="ah-r">{{ $slot }}</div>
</div>