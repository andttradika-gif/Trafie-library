@php
    // Gambar ilustrasi opsional: public/img/landing/{nama}.(jpg|jpeg|png|webp)
    $img = function ($base) {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $e) {
            if (file_exists(public_path("img/landing/$base.$e"))) {
                return asset("img/landing/$base.$e");
            }
        }
        return null;
    };
    $imgHero = $img('hero');
    $imgChallenge = $img('challenge');
    $imgLumi = $img('lumi');
    $imgFooter = $img('footer');
    $imgLogo = $img('logo');

    // Data asli
    $totalBuku  = \App\Models\Book::count();
    $totalSiswa = \App\Models\User::where('role', 'student')->count();
    $tersedia   = \App\Models\BookCopy::where('status', 'Available')->count();
    $pinjamBulan = \App\Models\Loan::whereIn('status', ['Borrowed', 'Returned'])
        ->where('approved_at', '>=', now()->startOfMonth())->count();

    $pilihan = \App\Models\Book::with('category')
        ->addSelect(['loans_total' => \App\Models\Loan::query()->selectRaw('count(*)')->whereColumn('loans.book_id', 'books.id')])
        ->withAvg('approvedReviews', 'rating')
        ->withCount('approvedReviews')
        ->orderByDesc('loans_total')
        ->orderByDesc('id')
        ->take(10)->get();

    $kategori = \App\Models\Category::withCount('books')->orderByDesc('books_count')->orderBy('name')->take(8)->get();
    $ulasan = \App\Models\BookReview::approved()->with(['user', 'book'])->latest()->take(3)->get();

    $kontak = config('library.contact');
    $sosial = config('library.social');

    $grad = ['linear-gradient(135deg,#1f3a5f,#2a5a8a)','linear-gradient(135deg,#f3e3c3,#e8c88f)','linear-gradient(135deg,#d9ccf5,#b79aef)','linear-gradient(135deg,#f7c8dc,#d9ccf5)','linear-gradient(135deg,#cfe8f7,#b9a8ee)'];

    $ikon = function ($nama) {
        $p = [
            'book'   => '<path d="M12 6c-2-1.5-5-2-8-1.5v13c3-.5 6 0 8 1.5 2-1.5 5-2 8-1.5v-13C17 4 14 4.5 12 6z"/><path d="M12 6v13"/>',
            'heart'  => '<path d="M12 20s-7-4.4-7-10a4 4 0 017-2.5A4 4 0 0119 10c0 5.6-7 10-7 10z"/>',
            'star'   => '<path d="M12 3l2.2 5.6L20 9l-4.5 3.8L17 19l-5-3.2L7 19l1.5-6.2L4 9l5.8-.4z"/>',
            'cube'   => '<path d="M12 3l8 4.5v9L12 21l-8-4.5v-9z"/><path d="M12 12l8-4.5M12 12v9M12 12L4 7.5"/>',
            'search' => '<circle cx="11" cy="11" r="6.5"/><path d="M16 16l5 5"/>',
            'bolt'   => '<path d="M13 3L5 13h6l-1 8 8-10h-6z"/>',
            'planet' => '<circle cx="12" cy="12" r="4.5"/><ellipse cx="12" cy="12" rx="10" ry="4" transform="rotate(-25 12 12)"/>',
            'leaf'   => '<path d="M5 19c0-8 5-14 15-14 0 10-6 15-14 15"/><path d="M5 19c3-5 6-8 10-10"/>',
            'scroll' => '<path d="M7 4h11a2 2 0 012 2v12a2 2 0 01-2 2H8"/><path d="M7 4a2 2 0 00-2 2v1h3"/><path d="M8 20a2 2 0 01-2-2V8"/><path d="M11 9h6M11 13h6"/>',
            'cap'    => '<path d="M2 9l10-5 10 5-10 5z"/><path d="M6 11v5c0 1.5 3 3 6 3s6-1.5 6-3v-5"/>',
            'users'  => '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.5 3-6 6.5-6s6.5 2.5 6.5 6"/><path d="M16 4.5a3.5 3.5 0 010 7"/><path d="M18 14.2c2 .7 3.5 2.6 3.5 5.8"/>',
            'swap'   => '<path d="M20 11a8 8 0 00-14.5-4.5L4 8"/><path d="M4 4v4h4"/><path d="M4 13a8 8 0 0014.5 4.5L20 16"/><path d="M20 20v-4h-4"/>',
            'mail'   => '<rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="M4 7l8 6 8-6"/>',
            'phone'  => '<path d="M6 3h3l1.5 4-2 1.5a11 11 0 006 6L16 12.5l4 1.5v3a2 2 0 01-2 2A15 15 0 013 5a2 2 0 013-2z"/>',
            'pin'    => '<path d="M12 21s7-6 7-11a7 7 0 10-14 0c0 5 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>',
            'insta'  => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r=".8" fill="currentColor"/>',
            'tiktok' => '<path d="M14 4v10.5a3.5 3.5 0 11-3.5-3.5"/><path d="M14 4c.4 2.3 1.9 3.8 4.5 4"/>',
            'youtube'=> '<rect x="2.5" y="5.5" width="19" height="13" rx="4"/><path d="M10 9.5l5 2.5-5 2.5z" fill="currentColor"/>',
            'search2'=> '<circle cx="11" cy="11" r="6"/><path d="M16 16l4 4"/>',
        ];
        return $p[$nama] ?? $p['book'];
    };

    $petaKategori = ['romance' => 'heart', 'fantasy' => 'star', 'non' => 'cube', 'fiksi' => 'book', 'novel' => 'book',
        'myster' => 'search', 'misteri' => 'search', 'thriller' => 'bolt', 'sci' => 'planet', 'sains' => 'planet',
        'self' => 'leaf', 'pengembangan' => 'leaf', 'sejarah' => 'scroll', 'pendidikan' => 'cap'];
    $ikonKategori = function ($slug) use ($petaKategori) {
        foreach ($petaKategori as $kunci => $ik) {
            if (str_contains($slug, $kunci)) { return $ik; }
        }
        return 'book';
    };
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} | Discover Your Next Favorite Universe</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700|cormorant-garamond:400,500,600,700,400i,500i,600i,700i" rel="stylesheet" />
    <style>
        :root { --ink:#2b2f6b; --ink2:#46426f; --muted:#8581a6; --p1:#b194ee; --p2:#8d69d6; --bd:rgba(206,194,244,.65); }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body.lp { margin:0; min-height:100vh; overflow-x:hidden; font-family:'Poppins',sans-serif; color:var(--ink2);
            background: radial-gradient(900px 500px at 0% 100%, rgba(247,200,220,.35), transparent 60%),
                        radial-gradient(900px 500px at 100% 55%, rgba(207,232,247,.45), transparent 60%), #fbf9ff; }
        a { color:inherit; }
        svg.ic { width:1em; height:1em; fill:none; stroke:currentColor; stroke-width:1.7; stroke-linecap:round; stroke-linejoin:round; }
        .serif { font-family:'Cormorant Garamond',serif; }

        /* ===== NAVBAR ===== */
        .lp-top { position:relative; }
        .lp-nav { position:absolute; z-index:20; top:10px; left:42px; right:42px; height:58px; display:flex; align-items:center; gap:2rem; padding:0 28px 0 24px;
            border-radius:999px; background:rgba(255,255,255,.88); backdrop-filter:blur(14px); -webkit-backdrop-filter:blur(14px);
            border:1px solid rgba(255,255,255,.95); box-shadow:0 8px 30px rgba(139,92,246,.13); }
        .lp-logo { display:flex; align-items:center; gap:.65rem; text-decoration:none; flex-shrink:0; }
        .lp-logo .mark { width:48px; height:48px; flex-shrink:0; }
        .lp-logo .mark img, .lp-logo .mark svg { width:100%; height:100%; object-fit:contain; }
        .lp-logo b { display:block; font-family:'Cormorant Garamond',serif; font-style:italic; font-weight:600; font-size:1.7rem; line-height:1; color:#3a3a7e; }
        .lp-logo small { display:block; margin-top:3px; font-size:.5rem; font-style:italic; color:var(--p2); letter-spacing:.01em; }
        .lp-links { display:flex; gap:2.2rem; margin:0 auto; }
        .lp-links a { position:relative; padding:.4rem 0; font-size:.78rem; font-weight:500; color:#4a4673; text-decoration:none; }
        .lp-links a:hover { color:var(--p2); }
        .lp-links a.on { color:#6d4fc2; }
        .lp-links a.on::after { content:''; position:absolute; left:0; right:0; bottom:-2px; height:2px; border-radius:2px; background:linear-gradient(90deg,var(--p1),var(--p2)); }
        .lp-search { position:relative; width:255px; flex-shrink:0; }
        .lp-search input { width:100%; height:36px; padding:0 14px 0 38px; border-radius:999px; border:1px solid var(--bd); background:rgba(255,255,255,.9); font-family:inherit; font-size:.68rem; color:var(--ink2); outline:none; }
        .lp-search input:focus { border-color:var(--p1); box-shadow:0 0 0 3px rgba(177,148,238,.25); }
        .lp-search svg { position:absolute; left:14px; top:50%; transform:translateY(-50%); width:15px; height:15px; color:var(--p2); }
        .lp-auth { display:flex; gap:.7rem; flex-shrink:0; }
        .lp-btn { display:inline-flex; align-items:center; justify-content:center; gap:.4rem; height:36px; padding:0 1.5rem; border-radius:999px; font-family:inherit; font-size:.72rem; font-weight:600; text-decoration:none; cursor:pointer; border:1px solid transparent; transition:transform .2s ease, box-shadow .2s ease; }
        .lp-btn:hover { transform:translateY(-2px); }
        .lp-btn.fill { color:#fff; background:linear-gradient(135deg,var(--p1),var(--p2)); box-shadow:0 8px 20px rgba(141,105,214,.35); }
        .lp-btn.line { color:#5b4aa0; background:rgba(255,255,255,.85); border-color:#bba8ee; }
        .lp-btn.big { height:44px; padding:0 1.7rem; font-size:.78rem; }

        /* ===== HERO ===== */
        .lp-hero { position:relative; min-height:390px; overflow:hidden;
            background: radial-gradient(700px 420px at 92% 15%, rgba(207,232,247,.95), transparent 60%),
                        radial-gradient(650px 420px at 62% 100%, rgba(217,204,245,.95), transparent 60%),
                        radial-gradient(520px 320px at 0% 0%, rgba(247,200,220,.75), transparent 60%), #f6f1ff;
            background-size:cover; background-position:right center; }
        .lp-hero.has-img::before { content:''; position:absolute; inset:0; z-index:1; background:linear-gradient(90deg, rgba(251,249,255,.82) 0%, rgba(251,249,255,.5) 32%, rgba(251,249,255,0) 58%); }
        .lp-hero-art { position:absolute; right:0; top:0; height:100%; width:58%; z-index:0; }
        .lp-hero-in { position:relative; z-index:2; padding:112px 24px 40px clamp(24px, 9vw, 137px); }
        .lp-badge { display:inline-flex; align-items:center; gap:.45rem; padding:.38rem .95rem .38rem .7rem; border-radius:999px; background:rgba(255,255,255,.9); border:1px solid var(--bd); font-size:.7rem; font-weight:500; color:#4a4673; }
        .lp-badge svg { width:15px; height:15px; color:var(--p2); }
        .lp-hero h1 { margin:.95rem 0 .8rem; font-family:'Cormorant Garamond',serif; font-weight:500; font-size:clamp(2.3rem,3.45vw,3.4rem); line-height:1.02; letter-spacing:-.01em; color:#2e2c5a; }
        .lp-hero h1 em { font-style:normal; background:linear-gradient(90deg,#8d69d6,#b79aef); -webkit-background-clip:text; background-clip:text; color:transparent; }
        .lp-hero p { margin:0 0 1.5rem; font-size:.88rem; line-height:1.55; color:#2f2c55; }
        .lp-hero .btns { display:flex; gap:.9rem; flex-wrap:wrap; }

        /* ===== KARTU ===== */
        .lp-wrap { max-width:1520px; margin:0 auto; padding:0 24px; }
        .lp-row { display:grid; gap:18px; margin-top:16px; }
        .lp-row.r1 { grid-template-columns:1.95fr 1fr .9fr; }
        .lp-row.r2 { grid-template-columns:1.78fr 1fr 1fr; }
        .lp-card { position:relative; overflow:hidden; padding:18px 20px 20px; border-radius:22px; background:rgba(255,255,255,.84); border:1px solid var(--bd);
            box-shadow:0 10px 34px rgba(139,92,246,.09); backdrop-filter:blur(10px); -webkit-backdrop-filter:blur(10px); }
        .lp-sh { display:flex; align-items:flex-start; gap:.6rem; }
        .lp-sh > svg { width:30px; height:30px; flex-shrink:0; color:var(--p2); margin-top:2px; }
        .lp-sh h2 { margin:0; font-family:'Cormorant Garamond',serif; font-weight:600; font-size:1.55rem; line-height:1.05; color:var(--ink); }
        .lp-sh p { margin:.2rem 0 0; font-size:.62rem; color:var(--muted); }
        .lp-sh .more { margin-left:auto; flex-shrink:0; font-size:.6rem; font-weight:500; color:var(--p2); text-decoration:none; padding-top:.35rem; }
        .lp-empty { margin:1.2rem 0 .4rem; font-size:.74rem; line-height:1.6; color:var(--muted); }

        /* carousel */
        .lp-car { position:relative; margin-top:14px; }
        .lp-track { display:flex; gap:14px; overflow-x:auto; scroll-behavior:smooth; scrollbar-width:none; padding:2px 34px 4px; }
        .lp-track::-webkit-scrollbar { display:none; }
        .lp-arrow { position:absolute; top:50%; transform:translateY(-50%); z-index:3; width:28px; height:28px; border-radius:50%; border:1px solid var(--bd); background:#fff; color:var(--p2); font-size:1.1rem; line-height:1; cursor:pointer; box-shadow:0 4px 12px rgba(139,92,246,.18); }
        .lp-arrow.prev { left:0; } .lp-arrow.next { right:0; }
        .lp-bk { position:relative; flex:0 0 132px; display:flex; flex-direction:column; padding:10px 10px 12px; border-radius:14px; background:#fff; border:1px solid var(--bd); text-decoration:none; transition:transform .2s ease, box-shadow .2s ease; }
        .lp-bk:hover { transform:translateY(-3px); box-shadow:0 12px 26px rgba(139,92,246,.16); }
        .lp-bk .bm { position:absolute; top:9px; right:9px; width:13px; height:13px; color:#9a96b8; }
        .lp-bk .cv { width:86px; height:128px; margin:2px auto 9px; border-radius:4px; overflow:hidden; display:flex; align-items:center; justify-content:center; text-align:center; padding:6px;
            font-family:'Cormorant Garamond',serif; font-weight:700; font-size:.78rem; line-height:1.15; color:#fff; box-shadow:0 8px 16px rgba(60,50,120,.22); }
        .lp-bk .cv img { width:100%; height:100%; object-fit:cover; display:block; margin:-6px; }
        .lp-bk h3 { margin:0; font-size:.66rem; font-weight:600; line-height:1.25; color:var(--ink); display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .lp-bk p { margin:.15rem 0 .35rem; font-size:.56rem; color:var(--muted); }
        .lp-bk .rt { display:flex; align-items:center; gap:.25rem; margin-bottom:.4rem; font-size:.58rem; color:#6b6790; }
        .lp-bk .rt svg { width:10px; height:10px; fill:#f5b83d; stroke:none; }
        .lp-bk .rt b { color:#4a4673; font-weight:600; }
        .lp-chip { align-self:flex-start; padding:.16rem .6rem; border-radius:6px; font-size:.52rem; font-weight:500; background:rgba(203,212,250,.5); color:#4f5aa3; margin-top:auto; }

        /* kategori */
        .lp-cats { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-top:16px; }
        .lp-cat { display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.45rem; height:84px; border-radius:14px; border:1px solid rgba(255,255,255,.9); text-decoration:none; font-size:.58rem; font-weight:500; color:#4a4673; transition:transform .2s ease; }
        .lp-cat:hover { transform:translateY(-3px); }
        .lp-cat svg { width:26px; height:26px; }
        .lp-cat.k0 { background:#e7f1fb; } .lp-cat.k0 svg { color:#5b8fc7; }
        .lp-cat.k1 { background:#fde8ef; } .lp-cat.k1 svg { color:#e0709a; }
        .lp-cat.k2 { background:#ece6fb; } .lp-cat.k2 svg { color:#8a6bd0; }
        .lp-cat.k3 { background:#e5f3ea; } .lp-cat.k3 svg { color:#5aa07a; }
        .lp-cat.k4 { background:#fdf0e3; } .lp-cat.k4 svg { color:#e09a52; }
        .lp-cat.k5 { background:#efe8fb; } .lp-cat.k5 svg { color:#8a6bd0; }
        .lp-cat.k6 { background:#e3f1fa; } .lp-cat.k6 svg { color:#4f95c9; }
        .lp-cat.k7 { background:#e6f4ec; } .lp-cat.k7 svg { color:#5ba37a; }

        /* statistik */
        .lp-stats { display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-top:16px; }
        .lp-stat { display:flex; align-items:center; gap:.65rem; padding:.9rem .8rem; min-height:84px; border-radius:14px; background:rgba(255,255,255,.9); border:1px solid var(--bd); }
        .lp-stat .ib { width:38px; height:38px; flex-shrink:0; display:flex; align-items:center; justify-content:center; border-radius:50%; font-size:1.15rem; }
        .lp-stat .ib.a { background:#f3e6fb; color:#a06cd5; } .lp-stat .ib.b { background:#e2f3ea; color:#4f9c76; }
        .lp-stat .ib.c { background:#e4edfb; color:#5b7fcf; } .lp-stat .ib.d { background:#e6f4ea; color:#58a077; }
        .lp-stat b { display:block; font-size:1.05rem; font-weight:600; color:var(--ink); line-height:1.15; }
        .lp-stat small { display:block; font-size:.56rem; color:var(--muted); }

        /* ulasan */
        .lp-revs { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; margin-top:14px; }
        .lp-rev { padding:14px 14px 12px; border-radius:14px; background:#fff; border:1px solid var(--bd); }
        .lp-rev .who { display:flex; align-items:center; gap:.6rem; }
        .lp-rev .av { width:36px; height:36px; flex-shrink:0; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:600; font-size:.85rem; color:#5b34a8; background:linear-gradient(135deg,#f7c8dc,#d9ccf5); border:2px solid #fff; box-shadow:0 3px 10px rgba(167,139,250,.3); }
        .lp-rev b { display:block; font-size:.68rem; font-weight:600; color:var(--ink); }
        .lp-rev small { display:block; font-size:.54rem; color:var(--muted); }
        .lp-rev q { display:block; margin:.7rem 0 .6rem; font-size:.64rem; line-height:1.55; color:#4a4673; quotes:none; }
        .lp-rev .st { color:#f5b83d; font-size:.72rem; letter-spacing:1px; }

        /* challenge + lumi */
        .lp-feat { min-height:208px; padding:22px 24px; background-size:cover; background-position:right center; }
        .lp-feat.ch { background:linear-gradient(120deg,rgba(255,255,255,.7),rgba(233,224,252,.9) 55%,rgba(247,200,220,.6)); }
        .lp-feat.lu { background:linear-gradient(120deg,rgba(255,255,255,.7),rgba(207,232,247,.85) 55%,rgba(217,204,245,.7)); }
        .lp-feat > * { position:relative; z-index:2; }
        .lp-feat h2 { margin:0; font-family:'Cormorant Garamond',serif; font-style:italic; font-weight:600; font-size:2rem; line-height:1.05; color:var(--ink); }
        .lp-feat .sub { margin:.25rem 0 0; font-size:.68rem; color:var(--muted); }
        .lp-feat p.t { margin:1rem 0 1.1rem; max-width:62%; font-size:.7rem; line-height:1.55; color:#46426f; }
        .lp-feat.ch p.t { margin-top:.9rem; }
        .lp-feat .art { position:absolute; right:10px; bottom:0; z-index:1; height:92%; width:44%; }
        .lp-feat .art svg, .lp-feat .art img { width:100%; height:100%; object-fit:contain; object-position:right bottom; }
        .lp-lumi-head { display:flex; align-items:center; gap:.5rem; }
        .lp-lumi-head svg { width:28px; height:28px; color:var(--p2); }
        .lp-bubble { position:absolute; right:12px; top:16px; z-index:3; width:118px; padding:.5rem .6rem; border-radius:14px; background:#fff; border:1px solid var(--bd); font-size:.52rem; line-height:1.4; color:#46426f; box-shadow:0 6px 16px rgba(139,92,246,.15); }
        .lp-bubble::after { content:''; position:absolute; bottom:-6px; left:26px; width:11px; height:11px; background:#fff; border-right:1px solid var(--bd); border-bottom:1px solid var(--bd); transform:rotate(45deg); }

        /* footer */
        .lp-foot { position:relative; margin:18px 24px 24px; border-radius:26px; overflow:hidden; border:1px solid var(--bd);
            background: radial-gradient(260px 160px at 0% 100%, rgba(247,200,220,.8), transparent 70%),
                        radial-gradient(260px 160px at 100% 100%, rgba(247,200,220,.8), transparent 70%),
                        radial-gradient(300px 140px at 18% 0%, rgba(217,204,245,.6), transparent 70%),
                        rgba(255,255,255,.85);
            background-size:cover; background-position:center; }
        .lp-foot-in { position:relative; z-index:1; max-width:1200px; margin:0 auto; padding:26px 24px; display:grid; grid-template-columns:1.25fr 1fr .8fr .8fr 1.3fr; gap:1.5rem; align-items:center; }
        .lp-foot .brand { display:flex; align-items:center; gap:.9rem; }
        .lp-foot .brand .mark { width:84px; height:84px; flex-shrink:0; }
        .lp-foot .brand .mark img, .lp-foot .brand .mark svg { width:100%; height:100%; object-fit:contain; }
        .lp-foot .brand b { display:block; font-family:'Cormorant Garamond',serif; font-weight:500; font-size:1.7rem; line-height:1; color:var(--ink); }
        .lp-foot .brand small { display:block; margin-top:.35rem; font-size:.52rem; color:var(--muted); max-width:170px; line-height:1.4; }
        .lp-foot h4 { margin:0 0 .6rem; font-size:.66rem; font-weight:600; color:var(--ink); }
        .lp-foot ul { margin:0; padding:0; list-style:none; }
        .lp-foot li { display:flex; align-items:center; gap:.5rem; margin-bottom:.4rem; font-size:.6rem; color:#55517c; }
        .lp-foot li svg { width:12px; height:12px; color:var(--muted); flex-shrink:0; }
        .lp-foot li a { text-decoration:none; }
        .lp-foot li a:hover { color:var(--p2); }
        .lp-foot .sep { border-left:1px solid var(--bd); padding-left:1.5rem; }
        .lp-social { display:flex; gap:.7rem; }
        .lp-social a { width:20px; height:20px; color:#55517c; }
        .lp-social a:hover { color:var(--p2); }
        .lp-social svg { width:100%; height:100%; }
        .lp-quote { text-align:right; font-family:'Cormorant Garamond',serif; font-style:italic; font-size:1.15rem; line-height:1.45; color:var(--ink); }
        .lp-quote small { display:block; margin-top:.4rem; font-size:.8rem; color:var(--muted); }

        /* ===== RESPONSIF ===== */
        @media (max-width:1180px) { .lp-search { display:none; } }
        @media (max-width:1100px) {
            .lp-row.r1, .lp-row.r2 { grid-template-columns:1fr; }
            .lp-foot-in { grid-template-columns:1fr 1fr; }
            .lp-foot .sep { border-left:0; padding-left:0; }
            .lp-quote { text-align:left; }
        }
        @media (max-width:900px) {
            .lp-nav { left:12px; right:12px; padding:0 14px; gap:1rem; }
            .lp-links { display:none; }
            .lp-auth { margin-left:auto; }
            .lp-logo small { display:none; }
            .lp-revs { grid-template-columns:1fr; }
        }
        @media (max-width:560px) {
            .lp-hero-in { padding-top:100px; }
            .lp-hero-art { opacity:.5; width:100%; }
            .lp-btn { padding:0 1.1rem; }
            .lp-feat p.t { max-width:100%; }
            .lp-feat .art { opacity:.35; }
            .lp-foot-in { grid-template-columns:1fr; }
            .lp-wrap { padding:0 14px; }
            .lp-foot { margin:18px 14px 20px; }
        }
    </style>
    <!-- trafie-icon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}?v=3">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}?v=3">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=3">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=3">
    <meta name="theme-color" content="#a78bfa">
    <!-- /trafie-icon -->
</head>
<body class="lp">

<div class="lp-top">
    <header class="lp-nav">
        <a href="{{ url('/') }}" class="lp-logo">
            <span class="mark">
                @if($imgLogo)<img src="{{ $imgLogo }}" alt="">@else
                <svg viewBox="0 0 64 64" fill="none" aria-hidden="true">
                    <defs><linearGradient id="lgA" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#cfe8f7"/><stop offset=".5" stop-color="#d9ccf5"/><stop offset="1" stop-color="#f7c8dc"/></linearGradient></defs>
                    <polygon points="32,4 46,24 32,60 18,24" fill="url(#lgA)" stroke="#a78bfa" stroke-width="1.5" stroke-linejoin="round"/>
                    <polygon points="32,4 46,24 18,24" fill="#fff" fill-opacity=".45"/>
                    <path d="M32 4 L32 60 M18 24 L46 24" stroke="#fff" stroke-opacity=".7"/>
                    <polygon points="14,34 20,44 14,56 8,44" fill="url(#lgA)" stroke="#f4a6c8" stroke-width="1"/>
                    <polygon points="50,32 55,41 50,52 45,41" fill="url(#lgA)" stroke="#9fd0ee" stroke-width="1"/>
                </svg>@endif
            </span>
            <span><b>Trafie-library</b><small>Where Every Book Blooms Into a Universe</small></span>
        </a>

        <nav class="lp-links">
            <a href="{{ url('/') }}" class="on">Home</a>
            <a href="{{ route('student.books.index') }}">Explore</a>
            <a href="#kategori">Categories</a>
            <a href="#reviews">Reviews</a>
            <a href="#about">About</a>
        </nav>

        <form class="lp-search" method="GET" action="{{ route('student.books.index') }}">
            <svg class="ic" viewBox="0 0 24 24">{!! $ikon('search2') !!}</svg>
            <input type="text" name="q" placeholder="Cari buku, penulis, atau kategori..." aria-label="Cari buku">
        </form>

        <div class="lp-auth">
            @auth
                <a href="{{ route('dashboard') }}" class="lp-btn fill">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="lp-btn line">Login</a>
                @if(Route::has('register'))<a href="{{ route('register') }}" class="lp-btn fill">Register</a>@endif
            @endauth
        </div>
    </header>

    <section class="lp-hero {{ $imgHero ? 'has-img' : '' }}" @if($imgHero) style="background-image:url('{{ $imgHero }}')" @endif>
        @unless($imgHero)
        <svg class="lp-hero-art" viewBox="0 0 800 400" preserveAspectRatio="xMaxYMax slice" aria-hidden="true">
            <defs>
                <linearGradient id="hc1" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff"/><stop offset=".55" stop-color="#d9ccf5"/><stop offset="1" stop-color="#b79aef"/></linearGradient>
                <linearGradient id="hc2" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff"/><stop offset="1" stop-color="#f4b6d2"/></linearGradient>
                <linearGradient id="hc3" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff"/><stop offset="1" stop-color="#9fd0ee"/></linearGradient>
            </defs>
            <polygon points="560,50 604,150 560,335 516,150" fill="url(#hc1)" stroke="#b9a8ee" opacity=".95"/>
            <polygon points="660,125 692,200 660,335 628,200" fill="url(#hc2)" stroke="#f4a6c8" opacity=".9"/>
            <polygon points="468,170 494,228 468,335 442,228" fill="url(#hc3)" stroke="#9fd0ee" opacity=".95"/>
            <polygon points="740,200 760,245 740,335 720,245" fill="url(#hc1)" stroke="#b9a8ee" opacity=".8"/>
            <path d="M390 335 C440 305 515 305 560 335 C605 305 680 305 730 335 L730 360 C680 330 605 330 560 360 C515 330 440 330 390 360 Z" fill="#fff" fill-opacity=".92" stroke="#c9b8f2"/>
            <g fill="#fff"><circle cx="400" cy="70" r="3"/><circle cx="700" cy="60" r="2.4"/><circle cx="340" cy="150" r="2"/><circle cx="770" cy="140" r="2.6"/></g>
            <path d="M610 40 l4 10 10 4 -10 4 -4 10 -4 -10 -10 -4 10 -4z" fill="#fff"/>
        </svg>
        @endunless

        <div class="lp-hero-in">
            <span class="lp-badge"><svg class="ic" viewBox="0 0 24 24">{!! $ikon('star') !!}</svg> Trafie-library</span>
            <h1>Discover Your Next<br>Favorite <em>Universe</em></h1>
            <p>Every book opens a new universe.<br>Find your next adventure in Trafie-library.</p>
            <div class="btns">
                <a href="{{ route('student.books.index') }}" class="lp-btn fill big">Explore Books &rarr;</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="lp-btn line big">Buka Dashboard</a>
                @else
                    <a href="{{ Route::has('register') ? route('register') : route('login') }}" class="lp-btn line big">Join the Library</a>
                @endauth
            </div>
        </div>
    </section>
</div>

<main class="lp-wrap">
    <div class="lp-row r1">
        {{-- Buku Pilihan --}}
        <section class="lp-card" id="buku">
            <div class="lp-sh">
                <svg class="ic" viewBox="0 0 24 24">{!! $ikon('star') !!}</svg>
                <div><h2>Buku Pilihan</h2><p>Buku-buku terbaik yang sedang populer di kalangan pembaca.</p></div>
                <a class="more" href="{{ route('student.books.index') }}">Lihat Semua &rarr;</a>
            </div>

            @if($pilihan->isEmpty())
                <p class="lp-empty">Belum ada buku di katalog. Admin bisa menambahkannya lewat menu Manajemen Buku.</p>
            @else
            <div class="lp-car" data-car>
                <button class="lp-arrow prev" type="button" aria-label="Sebelumnya">&lsaquo;</button>
                <div class="lp-track">
                    @foreach($pilihan as $b)
                    <a class="lp-bk" href="{{ route('student.books.show', $b) }}">
                        <svg class="ic bm" viewBox="0 0 24 24"><path d="M7 4h10v16l-5-3.5L7 20z"/></svg>
                        <div class="cv" style="background: {{ $grad[$b->id % 5] }}">
                            @if($b->cover_url)<img src="{{ $b->cover_url }}" alt="{{ $b->title }}">@else<span>{{ $b->title }}</span>@endif
                        </div>
                        <h3>{{ $b->title }}</h3>
                        <p>{{ $b->author }}</p>
                        @if($b->approved_reviews_count > 0)
                            <div class="rt"><svg class="ic" viewBox="0 0 24 24"><path d="M12 3l2.6 5.6 6 .7-4.4 4.1 1.2 6L12 16.5 6.6 19.4l1.2-6L3.4 9.3l6-.7z"/></svg><b>{{ number_format($b->approved_reviews_avg_rating, 1) }}</b> <span>({{ $b->approved_reviews_count }})</span></div>
                        @endif
                        <span class="lp-chip">{{ $b->category->name }}</span>
                    </a>
                    @endforeach
                </div>
                <button class="lp-arrow next" type="button" aria-label="Berikutnya">&rsaquo;</button>
            </div>
            @endif
        </section>

        {{-- Kategori Populer --}}
        <section class="lp-card" id="kategori">
            <div class="lp-sh">
                <svg class="ic" viewBox="0 0 24 24">{!! $ikon('star') !!}</svg>
                <div><h2>Kategori Populer</h2><p>Jelajahi berbagai kategori buku favoritmu.</p></div>
                <a class="more" href="{{ route('student.books.index') }}">Lihat Semua &rarr;</a>
            </div>
            @if($kategori->isEmpty())
                <p class="lp-empty">Belum ada kategori.</p>
            @else
            <div class="lp-cats">
                @foreach($kategori as $i => $c)
                    <a class="lp-cat k{{ $i % 8 }}" href="{{ route('student.books.index', ['kategori' => $c->slug]) }}">
                        <svg class="ic" viewBox="0 0 24 24">{!! $ikon($ikonKategori($c->slug)) !!}</svg>
                        <span>{{ $c->name }}</span>
                    </a>
                @endforeach
            </div>
            @endif
        </section>

        {{-- Statistik --}}
        <section class="lp-card">
            <div class="lp-sh">
                <svg class="ic" viewBox="0 0 24 24">{!! $ikon('star') !!}</svg>
                <div><h2>Statistik Perpustakaan</h2><p>Bersama kita membangun budaya membaca.</p></div>
            </div>
            <div class="lp-stats">
                <div class="lp-stat"><span class="ib a"><svg class="ic" viewBox="0 0 24 24">{!! $ikon('book') !!}</svg></span><div><b>{{ number_format($totalBuku) }}</b><small>Total Buku</small></div></div>
                <div class="lp-stat"><span class="ib b"><svg class="ic" viewBox="0 0 24 24">{!! $ikon('users') !!}</svg></span><div><b>{{ number_format($totalSiswa) }}</b><small>Siswa Terdaftar</small></div></div>
                <div class="lp-stat"><span class="ib c"><svg class="ic" viewBox="0 0 24 24">{!! $ikon('swap') !!}</svg></span><div><b>{{ number_format($pinjamBulan) }}</b><small>Peminjaman Bulan Ini</small></div></div>
                <div class="lp-stat"><span class="ib d"><svg class="ic" viewBox="0 0 24 24">{!! $ikon('star') !!}</svg></span><div><b>{{ number_format($tersedia) }}</b><small>Eksemplar Tersedia</small></div></div>
            </div>
        </section>
    </div>

    <div class="lp-row r2">
        {{-- Ulasan --}}
        <section class="lp-card" id="reviews">
            <div class="lp-sh">
                <svg class="ic" viewBox="0 0 24 24">{!! $ikon('star') !!}</svg>
                <div><h2>Apa Kata Mereka?</h2><p>Pendapat para pembaca tentang Trafie-library.</p></div>
            </div>
            @if($ulasan->isEmpty())
                <p class="lp-empty">Belum ada ulasan. Setelah mengembalikan buku, siswa bisa menulis ulasan di halaman detail buku, dan ulasan yang disetujui admin akan tampil di sini.</p>
            @else
            <div class="lp-revs">
                @foreach($ulasan as $r)
                    <div class="lp-rev">
                        <div class="who">
                            <span class="av">{{ strtoupper(mb_substr($r->user->name, 0, 1)) }}</span>
                            <div><b>{{ $r->user->name }}</b><small>tentang "{{ \Illuminate\Support\Str::limit($r->book->title, 24) }}"</small></div>
                        </div>
                        <q>{{ \Illuminate\Support\Str::limit($r->body, 150) }}</q>
                        <span class="st">{!! str_repeat('&#9733;', $r->rating) !!}</span>
                    </div>
                @endforeach
            </div>
            @endif
        </section>

        {{-- Reading Challenge (tampilan; fitur belum dibuat) --}}
        <section class="lp-card lp-feat ch" @if($imgChallenge) style="background-image:url('{{ $imgChallenge }}')" @endif>
            <h2>Reading Challenge</h2>
            <p class="t">Tantangan membaca untuk versi terbaik dirimu.</p>
            <a href="{{ auth()->check() ? route('dashboard') : route('login') }}" class="lp-btn fill">Lihat Challenge &rarr;</a>
            @unless($imgChallenge)
            <div class="art">
                <svg viewBox="0 0 200 200" aria-hidden="true">
                    <defs><linearGradient id="tr" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#fbe7a0"/><stop offset="1" stop-color="#e8b94f"/></linearGradient></defs>
                    <polygon points="40,150 55,95 70,150" fill="#d9ccf5" stroke="#b9a8ee"/>
                    <polygon points="150,160 165,100 180,160" fill="#f4c2da" stroke="#f4a6c8"/>
                    <path d="M75 40h60v40a30 30 0 01-60 0z" fill="url(#tr)" stroke="#d9a63f" stroke-width="2"/>
                    <path d="M75 52H58a14 14 0 0014 24M135 52h17a14 14 0 01-14 24" fill="none" stroke="#d9a63f" stroke-width="3"/>
                    <rect x="97" y="108" width="16" height="22" fill="#e8b94f"/>
                    <rect x="80" y="130" width="50" height="14" rx="3" fill="#d9a63f"/>
                    <path d="M105 56l4 9 10 1-7 7 2 10-9-5-9 5 2-10-7-7 10-1z" fill="#fff" fill-opacity=".85"/>
                </svg>
            </div>
            @endunless
        </section>

        {{-- Lumi AI (tampilan; fitur belum dibuat) --}}
        <section class="lp-card lp-feat lu">
            <div class="lp-lumi-head"><svg class="ic" viewBox="0 0 24 24">{!! $ikon('star') !!}</svg><h2>Lumi AI</h2></div>
            <p class="sub">Your Reading Companion</p>
            <p class="t">Tanya tentang buku, dapatkan rekomendasi yang sesuai dengan minat dan mood-mu!</p>
            <a href="{{ auth()->check() ? route('student.books.index') : route('login') }}" class="lp-btn fill">Chat with Lumi &rarr;</a>
            <div class="lp-bubble">Hai! Aku Lumi. Siap membantu rekomendasi buku untukmu!</div>
            <div class="art" style="width:38%">
                @if($imgLumi)<img src="{{ $imgLumi }}" alt="Lumi">@else
                <svg viewBox="0 0 120 130" aria-hidden="true">
                    <path d="M34 54L24 22 50 40z" fill="#fff" stroke="#cdbdf2" stroke-width="2" stroke-linejoin="round"/>
                    <path d="M86 54L96 22 70 40z" fill="#fff" stroke="#cdbdf2" stroke-width="2" stroke-linejoin="round"/>
                    <ellipse cx="60" cy="82" rx="36" ry="34" fill="#fff" stroke="#cdbdf2" stroke-width="2"/>
                    <circle cx="47" cy="82" r="6" fill="#6a7fd8"/><circle cx="73" cy="82" r="6" fill="#6a7fd8"/>
                    <circle cx="49" cy="80" r="2" fill="#fff"/><circle cx="75" cy="80" r="2" fill="#fff"/>
                    <ellipse cx="38" cy="94" rx="5" ry="3" fill="#f7b6cf"/><ellipse cx="82" cy="94" rx="5" ry="3" fill="#f7b6cf"/>
                    <path d="M55 94q5 4 10 0" fill="none" stroke="#9a86d8" stroke-width="2" stroke-linecap="round"/>
                    <path d="M60 50l3 6 6.5 1-4.8 4.6 1.2 6.4-5.9-3.1-5.9 3.1 1.2-6.4-4.8-4.6 6.5-1z" fill="#f5c542"/>
                </svg>@endif
            </div>
        </section>
    </div>
</main>

<footer class="lp-foot" id="about" @if($imgFooter) style="background-image:url('{{ $imgFooter }}')" @endif>
    <div class="lp-foot-in">
        <div class="brand">
            <span class="mark">
                @if($imgLogo)<img src="{{ $imgLogo }}" alt="">@else
                <svg viewBox="0 0 64 64" fill="none" aria-hidden="true">
                    <defs><linearGradient id="lgB" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#cfe8f7"/><stop offset=".5" stop-color="#d9ccf5"/><stop offset="1" stop-color="#f7c8dc"/></linearGradient></defs>
                    <polygon points="32,4 46,24 32,60 18,24" fill="url(#lgB)" stroke="#a78bfa" stroke-width="1.5" stroke-linejoin="round"/>
                    <polygon points="32,4 46,24 18,24" fill="#fff" fill-opacity=".45"/>
                    <path d="M32 4 L32 60 M18 24 L46 24" stroke="#fff" stroke-opacity=".7"/>
                </svg>@endif
            </span>
            <div><b>Trafie-library</b><small>Where Every Book Blooms Into a Universe</small></div>
        </div>

        <div class="sep">
            <h4>Hubungi Kami</h4>
            <ul>
                <li><svg class="ic" viewBox="0 0 24 24">{!! $ikon('mail') !!}</svg>{{ $kontak['email'] }}</li>
                <li><svg class="ic" viewBox="0 0 24 24">{!! $ikon('phone') !!}</svg>{{ $kontak['phone'] }}</li>
                <li><svg class="ic" viewBox="0 0 24 24">{!! $ikon('pin') !!}</svg>{{ $kontak['address'] }}</li>
            </ul>
        </div>

        <div>
            <h4>Tautan Cepat</h4>
            <ul>
                <li><a href="{{ url('/') }}">Home</a></li>
                <li><a href="{{ route('student.books.index') }}">Explore Books</a></li>
                <li><a href="#kategori">Categories</a></li>
                <li><a href="#about">About Us</a></li>
            </ul>
        </div>

        <div class="sep">
            <h4>Ikuti Kami</h4>
            <div class="lp-social">
                <a href="{{ $sosial['instagram'] }}" aria-label="Instagram"><svg class="ic" viewBox="0 0 24 24">{!! $ikon('insta') !!}</svg></a>
                <a href="{{ $sosial['tiktok'] }}" aria-label="TikTok"><svg class="ic" viewBox="0 0 24 24">{!! $ikon('tiktok') !!}</svg></a>
                <a href="{{ $sosial['youtube'] }}" aria-label="YouTube"><svg class="ic" viewBox="0 0 24 24">{!! $ikon('youtube') !!}</svg></a>
            </div>
        </div>

        <div class="lp-quote">&ldquo;Buku bukan hanya membuka pikiran, tapi juga membuka alam semesta baru.&rdquo;<small>&mdash; Trafie-library &mdash;</small></div>
    </div>
</footer>

<script>
document.querySelectorAll('[data-car]').forEach(function (w) {
    var t = w.querySelector('.lp-track');
    w.querySelector('.prev').addEventListener('click', function () { t.scrollBy({ left: -290, behavior: 'smooth' }); });
    w.querySelector('.next').addEventListener('click', function () { t.scrollBy({ left: 290, behavior: 'smooth' }); });
});
</script>
@include('partials.gloss')
@include('partials.skin')
@include('partials.statviz')
</body>
</html>