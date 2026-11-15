<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700|playfair-display:400,600,700" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        <div class="tl-auth">
            <div class="tl-auth-brand">
                <svg width="46" height="46" viewBox="0 0 64 64" fill="none" aria-hidden="true">
                    <defs><linearGradient id="gA" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#cfe8f7"/><stop offset=".5" stop-color="#d9ccf5"/><stop offset="1" stop-color="#f7c8dc"/></linearGradient></defs>
                    <polygon points="32,4 46,24 32,60 18,24" fill="url(#gA)" stroke="#a78bfa" stroke-width="1.5" stroke-linejoin="round"/>
                    <polygon points="32,4 46,24 18,24" fill="#fff" fill-opacity=".45"/>
                    <path d="M32 4 L32 60 M18 24 L46 24" stroke="#fff" stroke-opacity=".7"/>
                </svg>
                <h1>Trafie-library</h1>
                <p>Where Every Book Blooms Into a Universe</p>
            </div>
            {{ $slot }}
        </div>
    </div>
@include('partials.gloss')
@include('partials.skin')
@include('partials.statviz')
</body>
</html>