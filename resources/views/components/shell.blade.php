@props(['items' => [], 'role' => 'Siswa', 'title' => 'Dashboard'])
@php
    $ic = [
        'home' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M9 22V12h6v10"/>',
        'book' => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
        'grid' => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'bookmark' => '<path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'trend' => '<path d="M22 7l-8.5 8.5-5-5L2 17"/><path d="M16 7h6v6"/>',
        'heart' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 0 0 0-7.8z"/>',
        'compass' => '<circle cx="12" cy="12" r="9"/><path d="M16 8l-2 6-6 2 2-6z"/>',
        'gift' => '<path d="M20 12v9H4v-9"/><rect x="2" y="7" width="20" height="5" rx="1"/><path d="M12 21V7"/><path d="M12 7H8a2.5 2.5 0 0 1 0-5c3 0 4 5 4 5z"/><path d="M12 7h4a2.5 2.5 0 0 0 0-5c-3 0-4 5-4 5z"/>',
        'award' => '<circle cx="12" cy="8" r="6"/><path d="M8.2 13.5L7 22l5-3 5 3-1.2-8.5"/>',
        'star' => '<path d="M12 2l3 6.5 7 .9-5.1 4.8 1.3 7L12 17.8 5.8 21.2l1.3-7L2 9.4l7-.9z"/>',
        'bell' => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/>',
        'user' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'cog' => '<circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M4.2 4.2l2.1 2.1M17.7 17.7l2.1 2.1M2 12h3M19 12h3M4.2 19.8l2.1-2.1M17.7 6.3l2.1-2.1"/>',
        'sun' => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
        'chev' => '<path d="M6 9l6 6 6-6"/>',
        'dot' => '<circle cx="12" cy="12" r="9"/>',
        'layers' => '<path d="M12 2l10 5-10 5L2 7z"/><path d="M2 12l10 5 10-5"/><path d="M2 17l10 5 10-5"/>',

        'users' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/>',

        'refresh' => '<path d="M21 12a9 9 0 0 1-15.5 6.2L3 16"/><path d="M3 12a9 9 0 0 1 15.5-6.2L21 8"/><path d="M21 3v5h-5"/><path d="M3 21v-5h5"/>',

        'undo' => '<path d="M9 14L4 9l5-5"/><path d="M4 9h10.5a5.5 5.5 0 0 1 0 11H11"/>',

        'coin' => '<circle cx="12" cy="12" r="9"/><path d="M14.5 9a2.5 2 0 0 0-2.5-1.5c-1.4 0-2.5.8-2.5 2s1.1 1.7 2.5 2 2.5.8 2.5 2-1.1 2-2.5 2A2.5 2 0 0 1 9.5 15"/><path d="M12 6v1.5M12 16.5V18"/>',

        'card' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h4"/>',

        'megaphone' => '<path d="M3 11v2a1 1 0 0 0 1 1h2l7 4V6L6 10H4a1 1 0 0 0-1 1z"/><path d="M16.5 8.5a5 5 0 0 1 0 7"/><path d="M19 6a8.5 8.5 0 0 1 0 12"/>',

        'chart' => '<path d="M3 3v18h18"/><path d="M7 15l4-4 3 3 5-6"/>',

        'calendar' => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',

        'list' => '<path d="M8 6h13M8 12h13M8 18h13"/><circle cx="4" cy="6" r="1"/><circle cx="4" cy="12" r="1"/><circle cx="4" cy="18" r="1"/>',
        'refresh' => '<path d="M21 12a9 9 0 0 1-15.5 6.2L3 16"/><path d="M3 12A9 9 0 0 1 18.5 5.8L21 8"/><path d="M21 3v5h-5"/><path d="M3 21v-5h5"/>',
        'chart' => '<path d="M4 20V10"/><path d="M10 20V4"/><path d="M16 20v-7"/><path d="M22 20H2"/>',
        'users' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/>',
    ];
    $art = fn ($f) => file_exists(public_path('images/ds/' . $f)) ? asset('images/ds/' . $f) : null;
    $sbArt = $art('sbart.webp');
    $logo = $art('logo.webp');
    // ===== tl-admin-merge : menu admin lengkap sesuai desain =====
    $ic += [
        'layers' => '<path d="M12 2l10 5-10 5L2 7z"/><path d="M2 12l10 5 10-5"/><path d="M2 17l10 5 10-5"/>',
        'users' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/>',
        'refresh' => '<path d="M21 12a9 9 0 0 0-15.5-6.2L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 15.5 6.2L21 16"/><path d="M21 21v-5h-5"/>',
        'ret' => '<path d="M9 14L4 9l5-5"/><path d="M4 9h10a6 6 0 0 1 0 12h-3"/>',
        'coin' => '<circle cx="12" cy="12" r="9"/><path d="M14.5 9.2c-.4-.9-1.4-1.4-2.5-1.4-1.4 0-2.5.8-2.5 1.9s1 1.6 2.5 2 2.5.9 2.5 2-1.1 1.9-2.5 1.9c-1.2 0-2.2-.5-2.6-1.5"/><path d="M12 6.5v1.3M12 16.2v1.3"/>',
        'card' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/><path d="M6 15h4"/>',
        'mega' => '<path d="M3 11v2a1 1 0 0 0 1 1h3l6 4V6L7 10H4a1 1 0 0 0-1 1z"/><path d="M16.5 8.5a5 5 0 0 1 0 7"/>',
        'chart' => '<path d="M3 3v18h18"/><path d="M7 15l4-4 3 3 5-6"/>',
        'calendar' => '<rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/>',
        'log' => '<rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h4"/>',
    ];
    if ($role !== 'Siswa') {
        try {
            // [label, ikon, nama lama yang dikenali, nama route halaman baru (opsional)]
            $rancang = [
                ['Dashboard', 'home', ['dashboard'], null],
                ['Manajemen Buku', 'book', ['manajemen buku', 'buku'], null],
                ['Kategori Buku', 'grid', ['kategori buku', 'kategori'], null],
                ['Eksemplar Buku', 'layers', ['eksemplar buku', 'eksemplar'], 'extra.copies'],
                ['Manajemen Siswa', 'users', ['manajemen siswa', 'daftar siswa', 'siswa'], null],
                ['Peminjaman', 'bookmark', ['peminjaman'], null],
                ['Reservasi & Waiting List', 'clock', ['reservasi & waiting list', 'reservasi'], null],
                ['Perpanjangan', 'refresh', ['perpanjangan'], null],
                ['Pengembalian', 'ret', ['pengembalian'], 'extra.returns'],
                ['Denda', 'coin', ['denda'], 'extra.fines'],
                ['Pembayaran', 'card', ['pembayaran'], 'extra.payments'],
                ['Ulasan & Rating', 'star', ['ulasan & rating', 'ulasan'], null],
                ['Tantangan Membaca', 'award', ['tantangan membaca', 'tantangan'], null],
                ['Pengumuman', 'mega', ['pengumuman'], null],
                ['Laporan & Statistik', 'chart', ['laporan & statistik', 'laporan'], null],
                ['Kalender', 'calendar', ['kalender'], 'extra.calendar'],
                ['Log Aktivitas', 'log', ['log aktivitas', 'log'], 'extra.activity'],
                null,
                ['Pengaturan', 'cog', ['pengaturan'], null],
                ['Akun Saya', 'user', ['akun saya', 'akun'], null],
            ];
            $sisa = $items;
            $baru = [];
            foreach ($rancang as $r) {
                if ($r === null) {
                    $baru[] = ['divider' => true];
                    continue;
                }
                $hit = null;
                foreach ($sisa as $k => $it) {
                    if (!empty($it['divider'])) { continue; }
                    if (in_array(mb_strtolower(trim((string) ($it['label'] ?? ''))), $r[2], true)) { $hit = $k; break; }
                }
                $rt = null;
                if (!empty($r[3])) {
                    foreach (['admin.' . $r[3], $r[3]] as $nm) {
                        if (\Illuminate\Support\Facades\Route::has($nm)) { $rt = $nm; break; }
                    }
                }
                if ($hit !== null) {
                    $it = $sisa[$hit];
                    unset($sisa[$hit]);
                    $it['label'] = $r[0];
                    $it['icon'] = $r[1];
                    $baru[] = $it;
                } elseif ($rt !== null) {
                    $baru[] = ['route' => $rt, 'match' => $rt, 'label' => $r[0], 'icon' => $r[1]];
                } else {
                    $baru[] = ['label' => $r[0], 'icon' => $r[1], 'soon' => true];
                }
            }
            foreach ($sisa as $it) { $baru[] = $it; }
            $items = $baru;
        } catch (\Throwable $e) {
            // jika gagal, menu bawaan tetap dipakai
        }
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $role }} | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700|playfair-display:400,600,700,400i,600i" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/trafie-theme.css') }}?v={{ @filemtime(public_path('css/trafie-theme.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/tl-live.css') }}?v={{ filemtime(public_path('css/tl-live.css')) }}">
    <script src="{{ asset('js/tl-live.js') }}?v={{ filemtime(public_path('js/tl-live.js')) }}" defer></script>
    <!-- trafie-icon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=3">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}?v=3">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=3">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=3">
    <meta name="theme-color" content="#a78bfa">
    <!-- /trafie-icon -->
    <link rel="stylesheet" href="{{ asset('css/tl-ui.css') }}?v={{ filemtime(public_path('css/tl-ui.css')) }}">
    <script src="{{ asset('js/tl-ui.js') }}?v={{ filemtime(public_path('js/tl-ui.js')) }}" defer></script>
    <script src="{{ asset('js/tl-report.js') }}?v={{ filemtime(public_path('js/tl-report.js')) }}" defer></script>
<style>
/* ===== MOBILE: sidebar jadi drawer + burger ===== */
.nv-burger, .nv-close, .nv-overlay { display: none; }
@media (max-width: 900px) {
  html, body.tl-body-bg { overflow-x: hidden; }
  .tl-shell { height: 100vh; height: 100dvh; }
  .tl-shell > .nv {
    display: flex !important; position: fixed !important; left: 0; top: 0; bottom: 0; margin: 0 !important;
    width: min(310px, 86vw) !important; flex: none !important; height: 100vh !important; height: 100dvh !important;
    border-radius: 0 30px 30px 0 !important; z-index: 120 !important;
    transform: translateX(-108%); transition: transform .35s cubic-bezier(.22, .8, .24, 1); box-shadow: none;
  }
  body.nv-open .tl-shell > .nv { transform: none; box-shadow: 24px 0 60px rgba(60, 40, 130, .35); }
  .nv-overlay { display: block; position: fixed; inset: 0; z-index: 110; background: rgba(40, 28, 90, .42); -webkit-backdrop-filter: blur(5px); backdrop-filter: blur(5px); opacity: 0; pointer-events: none; transition: opacity .3s ease; }
  body.nv-open .nv-overlay { opacity: 1; pointer-events: auto; }
  body.nv-open { overflow: hidden; }
  .nv-close { display: grid; place-items: center; position: absolute; top: 14px; right: 14px; z-index: 8; width: 36px; height: 36px; padding: 0; border: 0; border-radius: 12px; cursor: pointer; font-size: 1.3rem; line-height: 1; color: #6d3fc4; background: rgba(255, 255, 255, .85); box-shadow: 0 6px 14px rgba(139, 92, 246, .2); }
  .nv-burger { display: grid; place-items: center; flex: 0 0 42px; width: 42px; height: 42px; padding: 0; border: 1px solid #fff; border-radius: 14px; cursor: pointer; color: #6d3fc4; background: linear-gradient(145deg, rgba(255, 255, 255, .95), rgba(236, 228, 255, .8)); box-shadow: 0 8px 18px rgba(139, 92, 246, .18), inset 0 1px 0 #fff; -webkit-tap-highlight-color: transparent; }
  .nv-burger svg { width: 22px; height: 22px; fill: none; stroke: currentColor; stroke-width: 2.2; stroke-linecap: round; }
  .nv-burger:active { transform: scale(.94); }
  .nv-i { height: 46px !important; font-size: 13px !important; }
  .nv-ic { flex-basis: 34px !important; width: 34px !important; height: 34px !important; }

  html body.tl-body-bg .tl-topbar { height: 62px; padding: 0 .9rem !important; gap: .55rem !important; }
  html body.tl-body-bg .tl-topbar > * { flex-shrink: 0; }
  html body.tl-body-bg .tl-topbar .judul { flex: 1 1 auto !important; min-width: 0; margin-right: 0 !important; font-size: 1.05rem !important; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
  html body.tl-body-bg .tl-topbar .tl-search { flex: 1 1 auto; min-width: 0; margin-right: 0 !important; padding: .45rem .8rem; }
  html body.tl-body-bg .tl-main { padding: 1rem !important; padding-bottom: calc(1.2rem + env(safe-area-inset-bottom)) !important; }
  html body.tl-body-bg .tl-main .tl-card { overflow-x: auto; -webkit-overflow-scrolling: touch; }
  html body.tl-body-bg .tl-main .tl-card table { min-width: 620px; }
}
@media (max-width: 640px) {
  html body.tl-body-bg .tl-topbar .tl-pill { display: none !important; }
  html body.tl-body-bg .tl-topbar .tl-user > div:not(.tl-avatar) { display: none !important; }
  html body.tl-body-bg .tl-topbar .tl-user { padding-left: 0 !important; border-left: 0 !important; }
  html body.tl-body-bg .tl-topbar .tl-bell { width: 20px; height: 20px; }
  html body.tl-body-bg .tl-main { padding: .8rem !important; }
  .dz-hero h1 { font-size: 1.6rem !important; } .dz-hero { padding: 1.3rem 1.2rem !important; }
  .ah { padding: 1rem 1.1rem !important; } .ah h1 { font-size: 1.3rem !important; } .ah-ico { flex-basis: 44px !important; width: 44px !important; height: 44px !important; }
  .ah-r { width: 100%; } .ah-r .ah-btn { flex: 1 1 auto; justify-content: center; }
}
@media (prefers-reduced-motion: reduce) { .tl-shell > .nv, .nv-overlay { transition: none !important; } }
</style>
</head>
<body class="tl-body-bg">
<div class="tl-shell">
    @php
    $nvArt = file_exists(public_path('images/ds/sidebar.png')) ? asset('images/ds/sidebar.png') : null;
    $nvGroups = [
        'Dashboard' => 'Utama', 'Manajemen Buku' => 'Utama', 'Kategori Buku' => 'Utama', 'Eksemplar Buku' => 'Utama', 'Manajemen Siswa' => 'Utama', 'Daftar Siswa' => 'Utama',
        'Peminjaman' => 'Sirkulasi', 'Reservasi & Waiting List' => 'Sirkulasi', 'Perpanjangan' => 'Sirkulasi', 'Pengembalian' => 'Sirkulasi', 'Denda' => 'Sirkulasi', 'Pembayaran' => 'Sirkulasi',
        'Ulasan & Rating' => 'Komunitas', 'Ulasan' => 'Komunitas', 'Tantangan Membaca' => 'Komunitas', 'Pengumuman' => 'Komunitas',
        'Laporan & Statistik' => 'Insight', 'Laporan' => 'Insight', 'Kalender' => 'Insight', 'Log Aktivitas' => 'Insight',
        'Pengaturan' => 'Akun', 'Akun Saya' => 'Akun',
        'Book Catalog' => 'Jelajah', 'Categories' => 'Jelajah',
        'My Borrowings' => 'Pinjaman', 'Reservations' => 'Pinjaman',
        'Reading Progress' => 'Membaca', 'My Favorites' => 'Membaca', 'Reading Galaxy' => 'Membaca', 'Reading Wrapped' => 'Membaca', 'Reading Challenges' => 'Membaca', 'Achievements' => 'Membaca',
        'Notifications' => 'Akun', 'My Profile' => 'Akun', 'Settings' => 'Akun',
    ];
    $nvPrev = null;
@endphp
<aside class="nv" @if($nvArt) style="--sb-art: url('{{ $nvArt }}')" @endif>
    <button type="button" class="nv-close" aria-label="Tutup menu">&times;</button>
    <div class="nv-aurora"></div><div class="nv-frost"></div><div class="nv-art"></div>
    <svg class="nv-shards" viewBox="0 0 150 230" aria-hidden="true"><defs><linearGradient id="nvs1" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff" stop-opacity=".95"/><stop offset=".6" stop-color="#cfe3ff" stop-opacity=".7"/><stop offset="1" stop-color="#b8a4ff" stop-opacity=".75"/></linearGradient><linearGradient id="nvs2" x1="1" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#ffe0f0" stop-opacity=".95"/><stop offset="1" stop-color="#e29bd8" stop-opacity=".7"/></linearGradient></defs><polygon points="108,60 132,120 124,230 92,230 88,120" fill="url(#nvs1)"/><polygon points="108,60 132,120 108,128" fill="#fff" opacity=".7"/><path d="M108 60 L108 230" stroke="#fff" stroke-opacity=".6" stroke-width=".8"/><polygon points="58,120 76,160 70,230 46,230 42,160" fill="url(#nvs2)"/><polygon points="58,120 76,160 58,166" fill="#fff" opacity=".65"/><polygon points="136,150 150,180 150,230 128,230 124,182" fill="url(#nvs1)" opacity=".85"/></svg>
    <i class="nv-st" style="left:18%;top:9%;animation-delay:.2s;width:9px;height:9px"></i><i class="nv-st" style="left:86%;top:16%;animation-delay:1.1s"></i><i class="nv-st" style="left:70%;top:38%;animation-delay:2s;width:8px;height:8px"></i><i class="nv-st" style="left:10%;top:52%;animation-delay:.7s;width:10px;height:10px"></i><i class="nv-st" style="left:90%;top:60%;animation-delay:1.6s;width:9px;height:9px"></i>

    <div class="nv-brand">
        <span class="nv-logo"><img src="{{ asset('images/ds/logo-lotus.svg') }}" alt=""></span>
        <div><h1>Trafie-library</h1><p>Where Every Book Blooms Into a Universe</p></div>
    </div>

    <nav class="nv-nav">
        @foreach($items as $it)
            @php
                $lb = $it['label'] ?? '';
                $grp = $nvGroups[$lb] ?? $nvPrev;
                $showGrp = $grp && $grp !== $nvPrev;
                $nvPrev = $grp;
                $svg = $ic[$it['icon'] ?? 'dot'] ?? ($ic['dot'] ?? '<circle cx="12" cy="12" r="9"/>');
                $href = null;
                if (empty($it['soon'])) {
                    if (!empty($it['url'])) { $href = $it['url']; }
                    elseif (!empty($it['route']) && \Illuminate\Support\Facades\Route::has($it['route'])) { $href = route($it['route'], $it['params'] ?? []); }
                }
                $on = !empty($it['active']) || (!empty($it['match']) && request()->routeIs($it['match']));
            @endphp
            @if($showGrp)<div class="nv-grp">{{ $grp }}</div>@endif
            @if($href)
                <a href="{{ $href }}" class="nv-i {{ $on ? 'on' : '' }}"><span class="nv-ic"><svg viewBox="0 0 24 24">{!! $svg !!}</svg></span><span class="t">{{ $lb }}</span></a>
            @else
                <span class="nv-i" title="Segera hadir"><span class="nv-ic"><svg viewBox="0 0 24 24">{!! $svg !!}</svg></span><span class="t">{{ $lb }}</span></span>
            @endif
        @endforeach
    </nav>

    <form method="POST" action="{{ route('logout') }}" class="nv-out">
        @csrf
        <button type="submit"><span class="nv-ic"><svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg></span>Logout</button>
    </form>
</aside>
<div class="nv-overlay"></div>

    <div class="tl-content">
        <header class="tl-topbar">
            <button type="button" class="nv-burger" aria-label="Buka menu" aria-expanded="false"><svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
            @if($role === 'Siswa')
                <form class="tl-search" method="GET" action="{{ route('student.books.index') }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#8b87a8" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                    <input type="text" name="q" placeholder="Cari buku, penulis, atau kategori...">
                </form>
            @else
                <div class="judul">{{ $title }}</div>
            @endif
            <span class="tl-pill"><i></i>Pastel Dream<svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $ic['chev'] !!}</svg></span>
            <span class="tl-iconbtn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $ic['sun'] !!}</svg></span>
            <span class="tl-iconbtn"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $ic['bell'] !!}</svg></span>
            <span class="tl-vr"></span>
            <div class="tl-user">
                <div class="tl-avatar">@if(Auth::user()->avatar_url)<img src="{{ Auth::user()->avatar_url }}" alt="" style="width:100%;height:100%;object-fit:cover">@else{{ strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}@endif</div>
                <div><b>{{ Auth::user()->name }}</b><small>{{ $role }}</small></div>
                <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $ic['chev'] !!}</svg>
            </div>
        </header>
        <main class="tl-main">
            @if(session('success'))<div class="tl-alert ok">{{ session('success') }}</div>@endif
            @if(session('error'))<div class="tl-alert err">{{ session('error') }}</div>@endif
            @if($errors->any())
                <div class="tl-alert err"><b>Belum tersimpan:</b>
                    <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            {{ $slot }}
        </main>
    </div>
</div>
@include('partials.gloss')
@include('partials.skin')
@include('partials.statviz')
<script>
(function () {
    var b = document.body;
    function setOpen(o) {
        b.classList.toggle('nv-open', o);
        var t = document.querySelector('.nv-burger');
        if (t) { t.setAttribute('aria-expanded', o ? 'true' : 'false'); }
    }
    document.addEventListener('click', function (e) {
        if (e.target.closest('.nv-burger')) { setOpen(!b.classList.contains('nv-open')); return; }
        if (e.target.closest('.nv-overlay') || e.target.closest('.nv-close')) { setOpen(false); return; }
        if (e.target.closest('.nv a.nv-i')) { setOpen(false); }
    });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { setOpen(false); } });
    window.addEventListener('resize', function () { if (window.innerWidth > 900) { setOpen(false); } });
})();
</script>
</body>
</html>