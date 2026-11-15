<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700|playfair-display:400,600,700" rel="stylesheet" />
    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css'])
    @endif
    <!-- trafie-icon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=3">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}?v=3">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=3">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=3">
    <meta name="theme-color" content="#a78bfa">
    <!-- /trafie-icon -->
</head>
<body class="tl-body-bg">
<div class="tl-auth-wrap">
    <div class="tl-auth" style="text-align:center">
        <svg width="46" height="46" viewBox="0 0 64 64" fill="none" aria-hidden="true">
            <defs><linearGradient id="gE" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#cfe8f7"/><stop offset=".5" stop-color="#d9ccf5"/><stop offset="1" stop-color="#f7c8dc"/></linearGradient></defs>
            <polygon points="32,4 46,24 32,60 18,24" fill="url(#gE)" stroke="#a78bfa" stroke-width="1.5" stroke-linejoin="round"/>
            <polygon points="32,4 46,24 18,24" fill="#fff" fill-opacity=".45"/>
            <path d="M32 4 L32 60 M18 24 L46 24" stroke="#fff" stroke-opacity=".7"/>
        </svg>
        <p class="tl-err-code">{{ $code }}</p>
        <h1 class="tl-serif" style="margin:.4rem 0 .5rem;font-size:1.4rem;color:#2d2a4a">{{ $title }}</h1>
        <p style="margin:0 0 1.4rem;font-size:.82rem;line-height:1.6;color:#7b7796">{{ $message }}</p>
        <div class="tl-row" style="justify-content:center">
            <a class="tl-btn" href="{{ url('/') }}">Kembali ke Beranda</a>
            <a class="tl-link-plain" href="javascript:history.back()">Halaman sebelumnya</a>
        </div>
    </div>
</div>
@include('partials.gloss')
@include('partials.skin')
@include('partials.statviz')
</body>
</html>