@php
    use Illuminate\Support\Facades\DB;
    use App\Models\Book;
    use App\Models\BookCopy;
    use App\Models\Category;
    use App\Models\Loan;
    use App\Models\User;

    $img = fn ($f) => asset('images/ds/' . $f);
    $rp = fn ($n) => 'Rp ' . number_format((int) $n, 0, ',', '.');

    $totalJudul  = Book::count();
    $totalCopies = BookCopy::count();
    $totalSiswa  = User::where('role', 'student')->count();
    $menunggu    = Loan::where('status', 'Pending')->count();
    $dipinjam    = Loan::where('status', 'Borrowed')->count();
    $terlambat   = Loan::where('status', 'Borrowed')->whereDate('due_date', '<', today())->count();
    $kembali     = Loan::where('status', 'Returned')->count();
    $dendaTotal  = Loan::sum('fine_amount');
    $dendaBayar  = Loan::whereNotNull('fine_paid_at')->sum('fine_amount');
    $dendaBelum  = Loan::where('fine_amount', '>', 0)->whereNull('fine_paid_at')->sum('fine_amount');

    $wk = now()->startOfWeek();
    $wkc = function ($q) { try { return (int) $q->count(); } catch (\Throwable $e) { return null; } };
    $dBuku = $wkc(Book::where('created_at', '>=', $wk));
    $dCopy = $wkc(BookCopy::where('created_at', '>=', $wk));
    $dSiswa = $wkc(User::where('role', 'student')->where('created_at', '>=', $wk));
    $dPinjam = $wkc(Loan::where('status', 'Borrowed')->where('created_at', '>=', $wk));
    $dMenunggu = $wkc(Loan::where('status', 'Pending')->where('created_at', '>=', $wk));
    $dKembali = $wkc(Loan::where('status', 'Returned')->where('updated_at', '>=', $wk));

    $ic = [
        'book' => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
        'layers' => '<path d="M12 2l10 5-10 5L2 7z"/><path d="M2 12l10 5 10-5"/><path d="M2 17l10 5 10-5"/>',
        'users' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/>',
        'clip' => '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4h6v3H9z"/><path d="M9 12h6M9 16h4"/>',
        'clock' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
        'alert' => '<circle cx="12" cy="12" r="9"/><path d="M12 7v6M12 16.5v.5"/>',
        'cash' => '<rect x="2" y="6" width="20" height="12" rx="2"/><circle cx="12" cy="12" r="3"/>',
        'check' => '<circle cx="12" cy="12" r="9"/><path d="M8 12l3 3 5-6"/>',
    ];
    $stats = [
        ['Total Buku', number_format($totalJudul), 'book', 'pink', $dBuku],
        ['Total Eksemplar', number_format($totalCopies), 'layers', 'blue', $dCopy],
        ['Total Siswa', number_format($totalSiswa), 'users', 'green', $dSiswa],
        ['Peminjaman Aktif', number_format($dipinjam), 'clip', 'teal', $dPinjam],
        ['Permintaan Peminjaman', number_format($menunggu), 'clock', 'lav', $dMenunggu],
        ['Buku Terlambat', number_format($terlambat), 'alert', 'pink'],
        ['Total Denda', $rp($dendaTotal), 'cash', 'green'],
        ['Denda Terbayar', $rp($dendaBayar), 'check', 'blue'],
        ['Denda Belum Dibayar', $rp($dendaBelum), 'cash', 'amber'],
        ['Sudah Dikembalikan', number_format($kembali), 'check', 'teal', $dKembali],
    ];

    // Grafik
    $perBulan = array_fill(1, 12, 0);
    foreach (Loan::selectRaw('MONTH(created_at) as m, COUNT(*) as c')->whereYear('created_at', now()->year)->groupBy('m')->get() as $r) { $perBulan[(int) $r->m] = (int) $r->c; }
    $st = Loan::selectRaw('status, COUNT(*) as c')->groupBy('status')->pluck('c', 'status');
    $tepat = max(0, (int) ($st['Borrowed'] ?? 0) - $terlambat);
    $statusData = [
        ['Menunggu', (int) ($st['Pending'] ?? 0), '#f4b6d2'],
        ['Dipinjam', $tepat, '#a78bfa'],
        ['Terlambat', $terlambat, '#f48fb1'],
        ['Dikembalikan', (int) ($st['Returned'] ?? 0), '#9fd0ee'],
        ['Ditolak', (int) ($st['Rejected'] ?? 0), '#cfd6e6'],
    ];
    $statusTotal = array_sum(array_column($statusData, 1));
    $katColors = ['#a78bfa', '#f48fb1', '#9fd0ee', '#9bd3b4', '#f6c885', '#d9ccf5'];
    $kategori = Category::withCount('books')->orderByDesc('books_count')->take(6)->get();
    $katTotal = $kategori->sum('books_count');
    $statusVals = array_column($statusData, 1);
    $statusCols = array_column($statusData, 2);
    $katVals = $kategori->pluck('books_count')->values()->all();
    $katCols = array_slice($katColors, 0, max(1, $kategori->count()));
    $bulanVals = array_values($perBulan);

    // Top 5
    $topBuku = DB::table('loans')->join('books', 'books.id', '=', 'loans.book_id')
        ->select('books.title', 'books.author', DB::raw('COUNT(*) as c'))->groupBy('books.id', 'books.title', 'books.author')->orderByDesc('c')->take(5)->get();
    $topBaca = DB::table('loans')->join('books', 'books.id', '=', 'loans.book_id')->where('loans.status', 'Returned')
        ->select('books.title', 'books.author', DB::raw('COUNT(*) as c'))->groupBy('books.id', 'books.title', 'books.author')->orderByDesc('c')->take(5)->get();
    $topSiswa = DB::table('loans')->join('users', 'users.id', '=', 'loans.user_id')
        ->select('users.name', DB::raw('COUNT(*) as c'))->groupBy('users.id', 'users.name')->orderByDesc('c')->take(5)->get();
    $genre = DB::table('loans')->join('books', 'books.id', '=', 'loans.book_id')->join('categories', 'categories.id', '=', 'books.category_id')
        ->select('categories.name', DB::raw('COUNT(*) as c'))->groupBy('categories.id', 'categories.name')->orderByDesc('c')->take(5)->get();
    $genreMax = max(1, (int) ($genre->max('c') ?? 1));

    $pending = Loan::with(['user', 'book'])->where('status', 'Pending')->latest('id')->take(4)->get();
    $recent = Loan::with(['user', 'book'])->latest('updated_at')->take(4)->get();
    $label = ['Pending' => 'Mengajukan pinjam', 'Borrowed' => 'Buku dipinjam', 'Returned' => 'Buku dikembalikan', 'Rejected' => 'Permintaan ditolak'];

    // Kalender bulan ini
    $awal = now()->startOfMonth();
    $offset = $awal->dayOfWeekIso - 1;
    $hariBulan = now()->daysInMonth;
    $jatuhTempo = Loan::where('status', 'Borrowed')->whereYear('due_date', now()->year)->whereMonth('due_date', now()->month)->pluck('due_date')->map(fn ($d) => (int) \Carbon\Carbon::parse($d)->day)->unique()->all();
    $bulanId = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'][now()->month - 1];
    $grad = ['linear-gradient(135deg,#cfe8f7,#b9a8ee)','linear-gradient(135deg,#f7c8dc,#d9ccf5)','linear-gradient(135deg,#cfe5d6,#cfe8f7)','linear-gradient(135deg,#d9ccf5,#f7c8dc)'];
@endphp
<x-admin-layout title="Dashboard Admin">
<style>
/* ===== AX dashboard admin v3 ===== */
.ax-wrap{container-type:inline-size;container-name:axw;font-family:'Poppins',system-ui,sans-serif;color:#3b2f63;display:flex;flex-direction:column;gap:16px}
.ax-wrap *{box-sizing:border-box}
.ax{--mut:#8c82ad;display:grid;grid-template-columns:minmax(0,1fr);gap:16px}
.ax-l{display:flex;flex-direction:column;gap:16px;min-width:0}
.ax-r{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;align-items:start}
.ax-card,.ax-hero,.ax-clock,.ax-stat,.ax-lumi,.ax-gal,.ax-deco{background:rgba(255,255,255,.7);border:1px solid rgba(255,255,255,.95);border-radius:20px;box-shadow:0 12px 32px rgba(140,110,200,.15),inset 0 1px 0 #fff;backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);position:relative;overflow:hidden}
.ax-card{padding:16px 18px}
.ax-glow::before{content:'';position:absolute;right:-40px;top:-40px;width:150px;height:150px;border-radius:50%;background:radial-gradient(circle,rgba(244,143,177,.22),transparent 70%);pointer-events:none}
.ax-h{display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:12px;position:relative}
.ax-h h3{margin:0;font-family:'Playfair Display',Georgia,serif;font-size:15px;font-weight:600;color:#3b2f63;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;min-width:0}
.ax-h h3::before{content:'';display:inline-block;width:7px;height:7px;margin-right:8px;border-radius:2px;transform:rotate(45deg);background:linear-gradient(135deg,#a78bfa,#f48fb1);vertical-align:middle;box-shadow:0 0 8px rgba(167,139,250,.7)}
.ax-h span,.ax-h a{white-space:nowrap;flex:none;font-size:10.5px;color:var(--mut);text-decoration:none}
.ax-h a{color:#8b6fe0}
/* top */
.ax-top{display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:16px}
.ax-hero{min-height:190px;padding:28px 30px;background:radial-gradient(circle at 78% 30%,rgba(224,200,255,.85),transparent 55%),radial-gradient(circle at 92% 90%,rgba(255,201,226,.8),transparent 50%),linear-gradient(120deg,#fff,#efe6fc 60%,#fbe6f1)}
.ax-hero::after{content:'';position:absolute;right:0;top:0;bottom:0;width:70%;background:var(--art) right bottom/auto 100% no-repeat;pointer-events:none}
.ax-hero .txt{position:relative;z-index:2;max-width:38%}
.ax-hero small{font-family:'Playfair Display',Georgia,serif;font-size:15px;color:#5a4a8a}
.ax-hero h1{margin:4px 0 12px;font-family:'Playfair Display',Georgia,serif;font-size:30px;line-height:1.15;font-weight:600;color:#2f2457}
.ax-hero p{margin:0;font-size:12.5px;line-height:1.7;color:#6d6390}
.ax-clock{padding:20px 22px;min-height:190px;display:flex;align-items:flex-start;justify-content:space-between;gap:10px;background:linear-gradient(160deg,#fff,#efe6fc 70%,#fbe6f1)}
.ax-clock::after{content:'';position:absolute;left:0;right:0;bottom:0;height:44%;background:var(--art) right bottom/cover no-repeat;opacity:.95;pointer-events:none;-webkit-mask-image:linear-gradient(to top,#000 55%,transparent);mask-image:linear-gradient(to top,#000 55%,transparent)}
.ax-clock .cl-t,.ax-clock .ax-an{position:relative;z-index:2}
.ax-clock .cl-t{min-width:0;align-self:flex-start;padding-top:2px}
.ax-clock .ax-an{align-self:flex-start;margin-top:4px}
.ax-clock small{display:block;font-size:11px;color:#6d6390;margin-bottom:2px}
.ax-clock b{display:block;font-family:'Playfair Display',Georgia,serif;font-size:34px;line-height:1.1;font-weight:600;color:#2f2457;text-shadow:0 2px 10px rgba(167,139,250,.35)}
.ax-clock p{margin:8px 0 0;position:relative;z-index:3;font-size:10.5px;line-height:1.5;color:#7d7399;max-width:130px}
.ax-an{flex:none;width:128px;height:128px;filter:drop-shadow(0 10px 14px rgba(110,80,200,.35)) drop-shadow(0 0 14px rgba(255,255,255,.9))}
/* stats */
.ax-stats{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px}
.ax-stat{display:flex;align-items:center;gap:11px;padding:13px;min-height:82px;transition:transform .25s,box-shadow .25s}
.ax-stat::after{content:'';position:absolute;right:-22px;bottom:-26px;width:80px;height:80px;border-radius:50%;background:radial-gradient(circle,var(--tint,rgba(167,139,250,.25)),transparent 70%);pointer-events:none}
.ax-stat:hover{transform:translateY(-3px);box-shadow:0 18px 36px rgba(140,110,200,.24),inset 0 1px 0 #fff}
.ax-stat .i{flex:none;width:40px;height:40px;border-radius:50%;display:grid;place-items:center;box-shadow:inset 0 1px 0 rgba(255,255,255,.9),0 6px 14px rgba(140,110,200,.2)}
.ax-stat .i svg{width:20px;height:20px}
.ax-stat small{display:block;font-size:10px;color:var(--mut);line-height:1.25}
.ax-stat b{display:block;font-family:'Playfair Display',Georgia,serif;font-size:19px;line-height:1.2;font-weight:600;color:#2f2457;white-space:nowrap}
.ax-stat b.rp{font-size:14.5px}
.ax-stat em{display:block;font-style:normal;font-size:9.5px;color:#4cae8a}
.a-pink{--tint:rgba(244,143,177,.3)}.a-blue{--tint:rgba(111,182,232,.3)}.a-green{--tint:rgba(110,200,150,.3)}.a-teal{--tint:rgba(80,190,180,.3)}.a-lav{--tint:rgba(167,139,250,.35)}.a-amber{--tint:rgba(246,200,133,.4)}
.a-pink .i{background:#fde1ee;color:#e0689b}.a-blue .i{background:#dde9fb;color:#5b86d6}.a-green .i{background:#dff3e6;color:#47a574}.a-teal .i{background:#d9f1ee;color:#3a9e97}.a-lav .i{background:#e9e1fb;color:#7d62d6}.a-amber .i{background:#fdebd3;color:#d68a2c}
/* charts */
.ax-charts{display:grid;grid-template-columns:1.35fr 1fr 1fr;gap:16px}
.ax-canvas{height:176px;position:relative}
.ax-donut{display:flex;align-items:center;gap:12px}
.ax-ring{position:relative;width:132px;height:132px;flex:none}
.ax-ring canvas{width:100%!important;height:100%!important;position:relative;z-index:1}
.ax-ring .mid{position:absolute;inset:0;display:grid;place-content:center;text-align:center;pointer-events:none;z-index:2}
.ax-ring .mid b{font-family:'Playfair Display',Georgia,serif;font-size:19px;line-height:1.1;font-weight:600;color:#2f2457}
.ax-ring .mid small{font-size:9px;color:var(--mut)}
.ax-lg{list-style:none;margin:0;padding:0;flex:1;min-width:0}
.ax-lg li{display:flex;align-items:center;gap:7px;font-size:11px;color:#5d5380;padding:3.5px 0}
.ax-lg i{width:9px;height:9px;border-radius:50%;flex:none;box-shadow:0 0 8px currentColor}
.ax-lg em{white-space:nowrap;margin-left:auto;font-style:normal;color:var(--mut);font-size:10.5px}
/* lists */
.ax-tops,.ax-bottom{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
.ax-li{display:flex;align-items:center;gap:9px;padding:7px 0;border-top:1px solid rgba(167,139,250,.14);font-size:11px}
.ax-li:first-of-type{border-top:0}
.ax-li .n{width:16px;color:var(--mut);font-size:10.5px;text-align:center;flex:none}
.ax-li .th{width:26px;height:34px;border-radius:5px;flex:none;box-shadow:0 3px 8px rgba(100,80,170,.25)}
.ax-li .av{width:28px;height:28px;border-radius:50%;flex:none;display:grid;place-items:center;font-weight:600;font-size:11px;color:#fff;background:linear-gradient(135deg,#b9a8ee,#f4b6d2)}
.ax-li .dot{width:9px;height:9px;border-radius:50%;background:linear-gradient(135deg,#a78bfa,#f48fb1);flex:none;box-shadow:0 0 0 4px rgba(167,139,250,.15)}
.ax-li .t{flex:1;min-width:0}
.ax-li .t b{display:block;font-weight:600;font-size:11.5px;color:#3b2f63;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ax-li .t small{display:block;font-size:10px;color:var(--mut);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ax-li em{font-style:normal;font-weight:600;color:#6d56c9;font-size:11px}
.ax-bar{display:grid;grid-template-columns:72px 1fr 24px;align-items:center;gap:8px;padding:7px 0;font-size:11px;color:#4d4373}
.ax-bar span{white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ax-bar div{height:8px;border-radius:9px;background:#ece6f8;overflow:hidden}
.ax-bar i{display:block;height:100%;border-radius:9px;background:linear-gradient(90deg,#a78bfa,#f48fb1);box-shadow:0 0 10px rgba(167,139,250,.6)}
.ax-bar em{font-style:normal;color:var(--mut);text-align:right;font-size:10.5px}
.ax-empty{font-size:11.5px;color:var(--mut);padding:10px 0;line-height:1.6}
.ax-chal{display:flex;gap:12px;align-items:center}
.ax-chal .bd{width:54px;height:54px;border-radius:16px;flex:none;background:linear-gradient(135deg,#e9e1fb,#fde1ee);display:grid;place-items:center;color:#8b6fe0;box-shadow:inset 0 1px 0 #fff,0 8px 16px rgba(140,110,200,.2)}
.ax-lumi{min-height:150px;padding:16px 18px}
.ax-lumi img{position:absolute;right:10px;top:10px;width:80px;height:auto;filter:drop-shadow(0 8px 12px rgba(120,90,200,.3))}
.ax-lumi h3{margin:0;font-family:'Playfair Display',Georgia,serif;font-size:16px;font-weight:600;color:#3b2f63}
.ax-lumi p{margin:0 0 12px;font-size:10.5px;color:var(--mut)}
.ax-lumi .in{margin-top:22px;padding:10px 12px;border-radius:12px;background:rgba(255,255,255,.85);font-size:11px;color:#6d6390;box-shadow:inset 0 0 0 1px rgba(167,139,250,.18)}
.ax-gal{min-height:150px;padding:16px 18px;color:#fff;background:var(--gal) center/cover no-repeat,linear-gradient(135deg,#2c2160,#5b3f9e);border-color:rgba(255,255,255,.25);box-shadow:0 14px 34px rgba(60,40,140,.35)}
.ax-gal h3{margin:0 0 6px;font-family:'Playfair Display',Georgia,serif;font-size:16px;font-weight:600;color:#fff;text-shadow:0 0 14px rgba(255,255,255,.6)}
.ax-gal p{margin:0;font-size:11px;line-height:1.6;color:rgba(255,255,255,.92);max-width:55%}
/* right row */
.ax-nt{display:flex;align-items:center;gap:10px;padding:9px 0;border-top:1px solid rgba(167,139,250,.14);text-decoration:none;color:inherit}
.ax-nt:first-of-type{border-top:0}
.ax-nt .av{width:34px;height:34px;border-radius:50%;flex:none;display:grid;place-items:center;font-weight:600;font-size:12px;color:#fff;background:linear-gradient(135deg,#b9a8ee,#f4b6d2)}
.ax-nt .t{flex:1;min-width:0}
.ax-nt .t b{display:block;font-size:11.5px;font-weight:600;color:#3b2f63}
.ax-nt .t small{display:block;font-size:10px;color:var(--mut);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ax-nt em{font-style:normal;font-size:9.5px;color:var(--mut);flex:none}
.ax-deco{height:120px;background:var(--art) center/cover no-repeat,linear-gradient(135deg,#efe6fc,#fbe0ee)}
/* calendar */
.ax-calc{padding:0 0 14px;background:linear-gradient(180deg,rgba(255,255,255,.85),rgba(246,236,252,.75))}
.ax-cal-h{display:flex;align-items:flex-end;justify-content:space-between;padding:18px 20px 14px;margin-bottom:10px;background:linear-gradient(120deg,#b9a0f2,#f2a6cf);color:#fff;position:relative;overflow:hidden}
.ax-cal-h::before{content:'';position:absolute;right:-20px;top:-30px;width:130px;height:130px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,.55),transparent 70%)}
.ax-cal-h::after{content:'';position:absolute;right:56px;top:12px;width:9px;height:9px;background:#fff;transform:rotate(45deg);box-shadow:0 0 12px 3px rgba(255,255,255,.9),-36px 22px 0 -2px rgba(255,255,255,.8),30px 30px 0 -3px rgba(255,255,255,.7)}
.ax-cal-h small{display:block;font-size:10px;letter-spacing:.14em;text-transform:uppercase;opacity:.9}
.ax-cal-h h3{margin:0;font-family:'Playfair Display',Georgia,serif;font-size:22px;font-weight:600;color:#fff;text-shadow:0 2px 12px rgba(90,60,180,.35)}
.ax-cal-h .yr{font-family:'Playfair Display',Georgia,serif;font-size:15px;opacity:.95;position:relative;z-index:1}
.ax-cal{display:grid;grid-template-columns:repeat(7,1fr);gap:3px;text-align:center;font-size:11px;padding:0 14px}
.ax-cal b{font-weight:600;color:#9a8fc0;font-size:9.5px;padding-bottom:6px;letter-spacing:.04em}
.ax-cal span{display:grid;place-items:center;aspect-ratio:1;position:relative}
.ax-cal span i{font-style:normal;width:28px;height:28px;border-radius:50%;display:grid;place-items:center;color:#4d4373;transition:background .2s,transform .2s}
.ax-cal span:not(.e):hover i{background:#efe8fc;transform:scale(1.08)}
.ax-cal .today i{background:linear-gradient(135deg,#a78bfa,#f48fb1);color:#fff;font-weight:700;box-shadow:0 0 0 4px rgba(167,139,250,.2),0 6px 14px rgba(167,139,250,.6)}
.ax-cal .due i{background:#ece6f8;color:#6d56c9;font-weight:600}
.ax-cal .late i{background:#fde1ee;color:#e0689b;font-weight:600}
.ax-cal .due::after,.ax-cal .late::after{content:'';position:absolute;bottom:1px;left:50%;width:4px;height:4px;margin-left:-2px;border-radius:50%;background:#a78bfa;box-shadow:0 0 6px #a78bfa}
.ax-cal .late::after{background:#f48fb1;box-shadow:0 0 6px #f48fb1}
.ax-key{display:flex;gap:12px;justify-content:center;flex-wrap:wrap;font-size:9.5px;color:var(--mut);margin-top:12px}
.ax-key span{display:inline-flex;align-items:center;gap:5px}
.ax-key i{width:8px;height:8px;border-radius:50%;display:inline-block}
.ax-key i.due{background:#cdbcf5}.ax-key i.late{background:#f4b6d2}.ax-key i.today{background:#a78bfa}

/* ===== glow ===== */
.ax-card,.ax-stat,.ax-lumi{box-shadow:0 12px 30px rgba(140,110,200,.16),0 0 28px rgba(196,170,255,.38),inset 0 1px 0 #fff,inset 0 0 20px rgba(255,255,255,.55);border-color:rgba(255,255,255,.98)}
.ax-card:hover,.ax-stat:hover,.ax-lumi:hover{box-shadow:0 18px 40px rgba(140,110,200,.26),0 0 40px rgba(244,143,177,.5),inset 0 1px 0 #fff,inset 0 0 22px rgba(255,255,255,.6)}
.ax-hero,.ax-clock{box-shadow:0 16px 40px rgba(140,110,200,.22),0 0 46px rgba(200,170,255,.6),inset 0 1px 0 #fff,inset 0 0 26px rgba(255,255,255,.6)}
.ax-gal{box-shadow:0 14px 34px rgba(60,40,140,.38),0 0 36px rgba(167,139,250,.6)}
.ax-deco{box-shadow:0 14px 34px rgba(140,110,200,.2),0 0 44px rgba(244,143,177,.45),inset 0 1px 0 #fff}
.ax-calc{box-shadow:0 14px 34px rgba(140,110,200,.2),0 0 40px rgba(196,170,255,.5),inset 0 1px 0 #fff}
.ax-hero::before,.ax-clock::before,.ax-deco::before{content:'';position:absolute;top:-10%;bottom:-10%;width:70px;left:-140px;background:linear-gradient(100deg,transparent,rgba(255,255,255,.75),transparent);transform:skewX(-20deg);animation:axSweep 8s ease-in-out infinite;pointer-events:none;z-index:3}
.ax-clock::before{animation-delay:1.6s}.ax-deco::before{animation-delay:3.2s}
@keyframes axSweep{0%,55%{left:-140px}100%{left:115%}}
.ax-h h3,.ax-hero h1,.ax-lumi h3{text-shadow:0 0 14px rgba(167,139,250,.35)}
.ax-stat b,.ax-ring .mid b,.ax-clock b{text-shadow:0 0 16px rgba(167,139,250,.5)}
.ax-stat .i,.ax-chal .bd{box-shadow:inset 0 1px 0 rgba(255,255,255,.9),0 0 18px var(--tint,rgba(167,139,250,.5)),0 6px 14px rgba(140,110,200,.2)}
.ax-li .th,.ax-li .av,.ax-nt .av{box-shadow:0 0 12px rgba(167,139,250,.55),0 3px 8px rgba(100,80,170,.25)}
.ax-an{animation:axPulse 4s ease-in-out infinite}
@keyframes axPulse{50%{filter:drop-shadow(0 10px 14px rgba(110,80,200,.35)) drop-shadow(0 0 26px rgba(244,143,177,.85))}}
.ax-lumi img{animation:axFloat 5s ease-in-out infinite}
@keyframes axFloat{50%{transform:translateY(-6px)}}
@media (prefers-reduced-motion:reduce){.ax-hero::before,.ax-clock::before,.ax-deco::before,.ax-an,.ax-lumi img{animation:none}}
/* responsive (by container width) */
@container axw (min-width:1240px){.ax{grid-template-columns:minmax(0,1fr) 300px}.ax-r{grid-template-columns:1fr}.ax-tops,.ax-bottom{grid-template-columns:repeat(2,minmax(0,1fr))}}
@container axw (min-width:1560px){.ax-tops,.ax-bottom{grid-template-columns:repeat(4,minmax(0,1fr))}}
@container axw (max-width:940px){.ax-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.ax-charts{grid-template-columns:1fr 1fr}.ax-charts>.ax-card:first-child{grid-column:1/-1}.ax-tops,.ax-bottom{grid-template-columns:repeat(2,minmax(0,1fr))}.ax-top{grid-template-columns:1fr}}
@container axw (max-width:700px){.ax-r{grid-template-columns:1fr}.ax-charts,.ax-tops,.ax-bottom{grid-template-columns:1fr}.ax-hero .txt{max-width:60%}}
@media (prefers-reduced-motion:reduce){.ax-stat,.ax-cal span i{transition:none}}

/* ===== glow2 */
.ax-card,.ax-stat,.ax-lumi,.ax-hero,.ax-clock,.ax-gal,.ax-deco{--g1:rgba(196,170,255,.6);--g2:rgba(244,143,177,.55);border:1.5px solid rgba(255,255,255,.98)!important;animation:axBreath 5s ease-in-out infinite}
.ax-stat:nth-child(2n),.ax-card:nth-child(2n){animation-delay:-1.7s}
.ax-stat:nth-child(3n),.ax-card:nth-child(3n){animation-delay:-3.2s}
.ax-gal{--g1:rgba(150,120,255,.75);--g2:rgba(255,150,210,.6)}
@keyframes axBreath{0%,100%{box-shadow:0 12px 30px rgba(140,110,200,.2),0 0 0 3px rgba(196,170,255,.2),0 0 30px var(--g1),inset 0 1px 0 #fff,inset 0 0 22px rgba(255,255,255,.55)}50%{box-shadow:0 14px 34px rgba(140,110,200,.26),0 0 0 4px rgba(244,143,177,.22),0 0 48px var(--g2),inset 0 1px 0 #fff,inset 0 0 26px rgba(255,255,255,.65)}}
.ax-card:hover,.ax-stat:hover,.ax-lumi:hover{animation:none;box-shadow:0 18px 42px rgba(140,110,200,.3),0 0 0 4px rgba(244,143,177,.28),0 0 54px rgba(244,143,177,.65),inset 0 1px 0 #fff}
.ax-chal .bd,.ax-lumi .in{box-shadow:0 0 0 2px rgba(255,255,255,.9),0 0 20px rgba(167,139,250,.5)}
.ax-ring{filter:drop-shadow(0 0 14px rgba(167,139,250,.5))}
.ax-canvas{filter:drop-shadow(0 0 10px rgba(167,139,250,.28))}
.ax-cal-h{box-shadow:0 8px 24px rgba(167,139,250,.55)}
aside,header,[class*="sidebar"],[class*="topbar"],[class*="navbar"]{box-shadow:0 0 0 1px rgba(255,255,255,.95),0 0 38px rgba(196,170,255,.6),0 12px 34px rgba(140,110,200,.2)!important}
header button,header [class*="pill"],header [class*="chip"],header select{box-shadow:0 0 0 1px rgba(255,255,255,.95),0 0 18px rgba(196,170,255,.6)}
@media (prefers-reduced-motion:reduce){.ax-card,.ax-stat,.ax-lumi,.ax-hero,.ax-clock,.ax-gal,.ax-deco{animation:none}}
/* ===== /glow2 */
</style>
<div class="ax-wrap">
<div class="ax">
  <div class="ax-l">

    <div class="ax-top">
        <div class="ax-hero" style="--art: url('{{ $img('ax-hero.svg') }}')">
            <div class="txt">
                <small>Selamat datang kembali,</small>
                <h1>{{ Auth::user()->name }}</h1>
                <p>Semoga hari ini penuh dengan ide hebat dan buku-buku luar biasa.</p>
            </div>
        </div>
        <div class="ax-clock" style="--art: url('{{ $img('ax-clock.svg') }}')">
            <div class="cl-t">
                <small id="adDate">&nbsp;</small>
                <b id="adTime">--:--</b>
                <p>Ayo buat perpustakaan kita semakin hidup!</p>
            </div>
            <svg class="ax-an" viewBox="0 0 120 120" aria-hidden="true"><defs><linearGradient id="cbz" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#fff"/><stop offset=".45" stop-color="#d6c8f6"/><stop offset="1" stop-color="#f6bcd8"/></linearGradient><radialGradient id="cfc" cx=".4" cy=".3" r=".9"><stop offset="0" stop-color="#fff"/><stop offset="1" stop-color="#ece4fb"/></radialGradient><filter id="chs" x="-30%" y="-30%" width="160%" height="160%"><feDropShadow dx="0" dy="1.5" stdDeviation="1.2" flood-color="#5b3fb0" flood-opacity=".35"/></filter></defs><circle cx="60" cy="60" r="58" fill="url(#cbz)"/><circle cx="60" cy="60" r="52" fill="url(#cfc)" stroke="#fff" stroke-width="1.5"/><circle cx="60" cy="60" r="52" fill="none" stroke="#b9a8ee" stroke-opacity=".5"/><line x1="60.00" y1="13.00" x2="60.00" y2="17.50" stroke="#7d62d6" stroke-width="1.6" stroke-linecap="round"/><line x1="65.12" y1="11.27" x2="64.86" y2="13.75" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="70.19" y1="12.07" x2="69.67" y2="14.52" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="75.14" y1="13.40" x2="74.37" y2="15.78" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="79.93" y1="15.24" x2="78.91" y2="17.52" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="83.50" y1="19.30" x2="81.25" y2="23.19" stroke="#7d62d6" stroke-width="1.6" stroke-linecap="round"/><line x1="88.80" y1="20.36" x2="87.33" y2="22.38" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="92.79" y1="23.59" x2="91.11" y2="25.44" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="96.41" y1="27.21" x2="94.56" y2="28.89" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="99.64" y1="31.20" x2="97.62" y2="32.67" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="100.70" y1="36.50" x2="96.81" y2="38.75" stroke="#7d62d6" stroke-width="1.6" stroke-linecap="round"/><line x1="104.76" y1="40.07" x2="102.48" y2="41.09" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="106.60" y1="44.86" x2="104.22" y2="45.63" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="107.93" y1="49.81" x2="105.48" y2="50.33" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="108.73" y1="54.88" x2="106.25" y2="55.14" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="107.00" y1="60.00" x2="102.50" y2="60.00" stroke="#7d62d6" stroke-width="1.6" stroke-linecap="round"/><line x1="108.73" y1="65.12" x2="106.25" y2="64.86" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="107.93" y1="70.19" x2="105.48" y2="69.67" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="106.60" y1="75.14" x2="104.22" y2="74.37" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="104.76" y1="79.93" x2="102.48" y2="78.91" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="100.70" y1="83.50" x2="96.81" y2="81.25" stroke="#7d62d6" stroke-width="1.6" stroke-linecap="round"/><line x1="99.64" y1="88.80" x2="97.62" y2="87.33" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="96.41" y1="92.79" x2="94.56" y2="91.11" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="92.79" y1="96.41" x2="91.11" y2="94.56" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="88.80" y1="99.64" x2="87.33" y2="97.62" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="83.50" y1="100.70" x2="81.25" y2="96.81" stroke="#7d62d6" stroke-width="1.6" stroke-linecap="round"/><line x1="79.93" y1="104.76" x2="78.91" y2="102.48" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="75.14" y1="106.60" x2="74.37" y2="104.22" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="70.19" y1="107.93" x2="69.67" y2="105.48" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="65.12" y1="108.73" x2="64.86" y2="106.25" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="60.00" y1="107.00" x2="60.00" y2="102.50" stroke="#7d62d6" stroke-width="1.6" stroke-linecap="round"/><line x1="54.88" y1="108.73" x2="55.14" y2="106.25" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="49.81" y1="107.93" x2="50.33" y2="105.48" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="44.86" y1="106.60" x2="45.63" y2="104.22" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="40.07" y1="104.76" x2="41.09" y2="102.48" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="36.50" y1="100.70" x2="38.75" y2="96.81" stroke="#7d62d6" stroke-width="1.6" stroke-linecap="round"/><line x1="31.20" y1="99.64" x2="32.67" y2="97.62" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="27.21" y1="96.41" x2="28.89" y2="94.56" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="23.59" y1="92.79" x2="25.44" y2="91.11" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="20.36" y1="88.80" x2="22.38" y2="87.33" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="19.30" y1="83.50" x2="23.19" y2="81.25" stroke="#7d62d6" stroke-width="1.6" stroke-linecap="round"/><line x1="15.24" y1="79.93" x2="17.52" y2="78.91" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="13.40" y1="75.14" x2="15.78" y2="74.37" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="12.07" y1="70.19" x2="14.52" y2="69.67" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="11.27" y1="65.12" x2="13.75" y2="64.86" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="13.00" y1="60.00" x2="17.50" y2="60.00" stroke="#7d62d6" stroke-width="1.6" stroke-linecap="round"/><line x1="11.27" y1="54.88" x2="13.75" y2="55.14" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="12.07" y1="49.81" x2="14.52" y2="50.33" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="13.40" y1="44.86" x2="15.78" y2="45.63" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="15.24" y1="40.07" x2="17.52" y2="41.09" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="19.30" y1="36.50" x2="23.19" y2="38.75" stroke="#7d62d6" stroke-width="1.6" stroke-linecap="round"/><line x1="20.36" y1="31.20" x2="22.38" y2="32.67" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="23.59" y1="27.21" x2="25.44" y2="28.89" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="27.21" y1="23.59" x2="28.89" y2="25.44" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="31.20" y1="20.36" x2="32.67" y2="22.38" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="36.50" y1="19.30" x2="38.75" y2="23.19" stroke="#7d62d6" stroke-width="1.6" stroke-linecap="round"/><line x1="40.07" y1="15.24" x2="41.09" y2="17.52" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="44.86" y1="13.40" x2="45.63" y2="15.78" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="49.81" y1="12.07" x2="50.33" y2="14.52" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><line x1="54.88" y1="11.27" x2="55.14" y2="13.75" stroke="#c9bde8" stroke-width="0.8" stroke-linecap="round"/><text x="60.0" y="28.4" text-anchor="middle" font-size="9.5" font-weight="600" fill="#5a4a8a" font-family="Playfair Display,Georgia,serif">12</text><text x="95.0" y="63.4" text-anchor="middle" font-size="9.5" font-weight="600" fill="#5a4a8a" font-family="Playfair Display,Georgia,serif">3</text><text x="60.0" y="98.4" text-anchor="middle" font-size="9.5" font-weight="600" fill="#5a4a8a" font-family="Playfair Display,Georgia,serif">6</text><text x="25.0" y="63.4" text-anchor="middle" font-size="9.5" font-weight="600" fill="#5a4a8a" font-family="Playfair Display,Georgia,serif">9</text><path d="M60 14l3 4-3 4-3-4z" fill="#f48fb1"/><g filter="url(#chs)"><line id="axH" x1="60" y1="60" x2="60" y2="38" stroke="#4b3a8f" stroke-width="3.2" stroke-linecap="round"/><line id="axM" x1="60" y1="60" x2="60" y2="27" stroke="#6d56c9" stroke-width="2.4" stroke-linecap="round"/><g id="axS"><line x1="60" y1="68" x2="60" y2="22" stroke="#f06fa0" stroke-width="1.1" stroke-linecap="round"/><circle cx="60" cy="66" r="2" fill="#f06fa0"/></g></g><circle cx="60" cy="60" r="3.4" fill="#fff" stroke="#a78bfa" stroke-width="1.6"/><path d="M18 40Q38 14 78 12Q44 24 24 56Z" fill="#fff" opacity=".45"/></svg>
        </div>
    </div>

    <div class="ax-stats">
        @foreach($stats as $s)
        <div class="ax-stat a-{{ $s[3] }}">
            <span class="i"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">{!! $ic[$s[2]] !!}</svg></span>
            <div><small>{{ $s[0] }}</small><b class="{{ str_starts_with($s[1], 'Rp') ? 'rp' : '' }}">{{ $s[1] }}</b>@if(isset($s[4]) && $s[4] !== null)<em>+{{ $s[4] }} minggu ini</em>@endif</div>
        </div>
        @endforeach
    </div>

    <div class="ax-charts">
        <div class="ax-card ax-glow">
            <div class="ax-h"><h3>Statistik Peminjaman</h3><span>Tahun {{ now()->year }}</span></div>
            <div class="ax-canvas"><canvas id="chLine"></canvas></div>
        </div>
        <div class="ax-card ax-glow">
            <div class="ax-h"><h3>Status Peminjaman</h3></div>
            <div class="ax-donut">
                <div class="ax-ring"><canvas id="chStatus"></canvas><div class="mid"><b>{{ $statusTotal }}</b><small>Total</small></div></div>
                <ul class="ax-lg">
                    @foreach($statusData as $d)<li><i style="background:{{ $d[2] }}"></i>{{ $d[0] }}<em>{{ $statusTotal ? round($d[1] / $statusTotal * 100) : 0 }}% ({{ $d[1] }})</em></li>@endforeach
                </ul>
            </div>
        </div>
        <div class="ax-card ax-glow">
            <div class="ax-h"><h3>Kategori Buku</h3></div>
            <div class="ax-donut">
                <div class="ax-ring"><canvas id="chKat"></canvas><div class="mid"><b>{{ $totalJudul }}</b><small>Total Buku</small></div></div>
                <ul class="ax-lg">
                    @forelse($kategori as $k)<li><i style="background:{{ $katColors[$loop->index % 6] }}"></i>{{ $k->name }}<em>{{ $katTotal ? round($k->books_count / $katTotal * 100) : 0 }}%</em></li>@empty<li>Belum ada kategori</li>@endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="ax-tops">
        <div class="ax-card">
            <div class="ax-h"><h3>5 Buku Sering Dipinjam</h3></div>
            @forelse($topBuku as $b)
            <div class="ax-li"><span class="n">{{ $loop->iteration }}</span><span class="th" style="background: {{ $grad[$loop->index % 4] }}"></span><div class="t"><b>{{ $b->title }}</b><small>{{ $b->author }}</small></div><em>{{ $b->c }}</em></div>
            @empty<div class="ax-empty">Belum ada data peminjaman.</div>@endforelse
        </div>
        <div class="ax-card">
            <div class="ax-h"><h3>5 Buku Sering Dibaca</h3></div>
            @forelse($topBaca as $b)
            <div class="ax-li"><span class="n">{{ $loop->iteration }}</span><span class="th" style="background: {{ $grad[($loop->index + 2) % 4] }}"></span><div class="t"><b>{{ $b->title }}</b><small>{{ $b->author }}</small></div><em>{{ $b->c }}</em></div>
            @empty<div class="ax-empty">Belum ada buku yang dikembalikan.</div>@endforelse
        </div>
        <div class="ax-card">
            <div class="ax-h"><h3>Siswa Paling Aktif</h3></div>
            @forelse($topSiswa as $u)
            <div class="ax-li"><span class="n">{{ $loop->iteration }}</span><span class="av">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span><div class="t"><b>{{ $u->name }}</b><small>total pinjam</small></div><em>{{ $u->c }}</em></div>
            @empty<div class="ax-empty">Belum ada data peminjaman.</div>@endforelse
        </div>
        <div class="ax-card">
            <div class="ax-h"><h3>Genre Populer</h3></div>
            @forelse($genre as $g)
            <div class="ax-bar"><span>{{ $g->name }}</span><div><i style="width: {{ round($g->c / $genreMax * 100) }}%"></i></div><em>{{ $g->c }}</em></div>
            @empty<div class="ax-empty">Belum ada data peminjaman.</div>@endforelse
        </div>
    </div>

    <div class="ax-bottom">
        <div class="ax-card">
            <div class="ax-h"><h3>Aktivitas Terbaru</h3><a href="{{ route('admin.loans.index') }}">Lihat Semua &rarr;</a></div>
            @forelse($recent as $a)
            <div class="ax-li"><span class="dot"></span><div class="t"><b>{{ $label[$a->status] ?? $a->status }}</b><small>{{ $a->book->title ?? 'Buku' }} &middot; {{ $a->user->name ?? '-' }}</small></div></div>
            @empty<div class="ax-empty">Belum ada aktivitas.</div>@endforelse
        </div>
        <div class="ax-card">
            <div class="ax-h"><h3>Reading Challenge</h3></div>
            <div class="ax-chal"><span class="bd"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"><path d="M12 3l2.6 5.3 5.9.9-4.3 4.1 1 5.8L12 16.3 6.8 19.1l1-5.8L3.5 9.2l5.9-.9z"/></svg></span><div class="ax-empty" style="padding:0">Belum ada tantangan aktif. Fitur tantangan membaca dibuat di Fase 4.</div></div>
        </div>
        <div class="ax-lumi">
            <img src="{{ $img('ax-lumi.svg') }}" alt="">
            <h3>Lumi AI</h3><p>Your Library Companion</p>
            <div class="in">Lumi segera hadir...</div>
        </div>
        <div class="ax-gal" style="--gal: url('{{ $img('ax-galaxy.svg') }}')">
            <h3>Reading Galaxy</h3>
            <p>Bintang muncul setiap siswa menyelesaikan buku (Fase 4).</p>
        </div>
    </div>

  </div>

  <div class="ax-r">
    <div class="ax-card">
        <div class="ax-h"><h3>Notifikasi Terbaru</h3><a href="{{ route('admin.loans.index', ['status' => 'Pending']) }}">Lihat Semua &rarr;</a></div>
        @forelse($pending as $p)
        <a class="ax-nt" href="{{ route('admin.loans.index', ['status' => 'Pending']) }}">
            <span class="av">{{ strtoupper(mb_substr($p->user->name ?? '?', 0, 1)) }}</span>
            <div class="t"><b>Permintaan peminjaman baru</b><small>{{ $p->user->name ?? '-' }} &middot; {{ $p->book->title ?? 'Buku' }}</small></div>
            <em>{{ $p->created_at ? $p->created_at->diffForHumans(null, true, true) : '' }}</em>
        </a>
        @empty<div class="ax-empty">Tidak ada permintaan yang menunggu.</div>@endforelse
    </div>

    <div class="ax-card ax-calc">
        <div class="ax-cal-h"><div><small>Kalender</small><h3>{{ $bulanId }}</h3></div><span class="yr">{{ now()->year }}</span></div>
        <div class="ax-cal">
            @foreach(['Sen','Sel','Rab','Kam','Jum','Sab','Min'] as $h)<b>{{ $h }}</b>@endforeach
            @for($i = 0; $i < $offset; $i++)<span class="e"></span>@endfor
            @for($d = 1; $d <= $hariBulan; $d++)
                @php $isToday = $d === now()->day; $isDue = in_array($d, $jatuhTempo); $isLate = $isDue && $d < now()->day; @endphp
                <span class="{{ $isToday ? 'today' : '' }} {{ $isLate ? 'late' : ($isDue ? 'due' : '') }}"><i>{{ $d }}</i></span>
            @endfor
        </div>
        <div class="ax-key"><span><i class="due"></i>Jatuh tempo</span><span><i class="late"></i>Terlambat</span><span><i class="today"></i>Hari ini</span></div>
    </div>

    <div class="ax-card">
        <div class="ax-h"><h3>Pengumuman Terbaru</h3></div>
        <div class="ax-empty">Belum ada pengumuman. Fitur pengumuman dibuat di Fase 5.</div>
    </div>
  </div>
</div>
<div class="ax-deco" style="--art: url('{{ $img('ax-deco.svg') }}')"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
(function () {
    var dEl = document.getElementById('adDate'), tEl = document.getElementById('adTime');
    var H = document.getElementById('axH'), M = document.getElementById('axM'), S = document.getElementById('axS');
    function digital() {
        var n = new Date();
        dEl.textContent = new Intl.DateTimeFormat('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }).format(n);
        tEl.textContent = n.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }).replace('.', ':');
    }
    function hands() {
        var n = new Date(), s = n.getSeconds() + n.getMilliseconds() / 1000, m = n.getMinutes() + s / 60, h = (n.getHours() % 12) + m / 60;
        if (H) { H.setAttribute('transform', 'rotate(' + (h * 30) + ' 60 60)'); }
        if (M) { M.setAttribute('transform', 'rotate(' + (m * 6) + ' 60 60)'); }
        if (S) { S.setAttribute('transform', 'rotate(' + (s * 6) + ' 60 60)'); }
        requestAnimationFrame(hands);
    }
    digital(); setInterval(digital, 5000); hands();

    if (typeof Chart === 'undefined') { return; }
    Chart.defaults.font.family = "'Poppins', sans-serif";
    Chart.defaults.color = '#8c82ad';
    var bulan = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    var glow = { id: 'axGlow', beforeDatasetsDraw: function (c) { c.ctx.save(); c.ctx.shadowColor = 'rgba(167,139,250,.55)'; c.ctx.shadowBlur = 16; c.ctx.shadowOffsetY = 5; }, afterDatasetsDraw: function (c) { c.ctx.restore(); } };
    var core = { id: 'axCore', beforeDraw: function (c) {
        var a = c.chartArea; if (!a) { return; }
        var x = (a.left + a.right) / 2, y = (a.top + a.bottom) / 2, r = Math.min(a.right - a.left, a.bottom - a.top) / 2 * .66;
        var g = c.ctx.createRadialGradient(x, y - r * .3, 2, x, y, r);
        g.addColorStop(0, 'rgba(255,255,255,.95)'); g.addColorStop(1, 'rgba(236,228,251,.65)');
        c.ctx.save(); c.ctx.fillStyle = g; c.ctx.beginPath(); c.ctx.arc(x, y, r, 0, Math.PI * 2); c.ctx.fill(); c.ctx.restore();
    } };

    var lc = document.getElementById('chLine').getContext('2d');
    var area = lc.createLinearGradient(0, 0, 0, 190);
    area.addColorStop(0, 'rgba(167,139,250,.38)'); area.addColorStop(.6, 'rgba(244,143,177,.14)'); area.addColorStop(1, 'rgba(255,255,255,0)');
    var vals = @json($bulanVals);
    new Chart(lc, {
        type: 'line',
        data: { labels: bulan, datasets: [{ data: vals, fill: true, backgroundColor: area, tension: .45, borderWidth: 3, pointRadius: 4.5, pointHoverRadius: 8, pointBackgroundColor: '#fff', pointBorderWidth: 2.5,
            borderColor: function (c) { var a = c.chart.chartArea; if (!a) { return '#a78bfa'; } var g = c.chart.ctx.createLinearGradient(a.left, 0, a.right, 0); g.addColorStop(0, '#a78bfa'); g.addColorStop(1, '#f48fb1'); return g; },
            pointBorderColor: function (c) { return c.dataIndex % 2 ? '#f48fb1' : '#a78bfa'; } }] },
        options: { maintainAspectRatio: false, interaction: { intersect: false, mode: 'index' }, plugins: { legend: { display: false }, tooltip: { backgroundColor: 'rgba(59,47,99,.94)', cornerRadius: 12, padding: 10, displayColors: false, titleFont: { weight: '600' } } },
            scales: { x: { grid: { display: false }, border: { display: false }, ticks: { font: { size: 10 } } }, y: { beginAtZero: true, suggestedMax: 5, border: { display: false }, ticks: { precision: 0, font: { size: 10 }, maxTicksLimit: 5 }, grid: { color: 'rgba(167,139,250,.16)', borderDash: [4, 6] } } } },
        plugins: [glow]
    });

    function lighten(h) { var n = parseInt(h.slice(1), 16), r = (n >> 16) & 255, g = (n >> 8) & 255, b = n & 255; return 'rgb(' + Math.min(255, r + 80) + ',' + Math.min(255, g + 80) + ',' + Math.min(255, b + 80) + ')'; }
    var halo = { id: 'axHalo', beforeDraw: function (c) {
        var a = c.chartArea; if (!a) { return; }
        var x = (a.left + a.right) / 2, y = (a.top + a.bottom) / 2, R = Math.min(a.right - a.left, a.bottom - a.top) / 2, t = c.ctx;
        t.save(); t.strokeStyle = 'rgba(167,139,250,.5)'; t.lineWidth = 1; t.setLineDash([2, 5]); t.beginPath(); t.arc(x, y, R + 4, 0, Math.PI * 2); t.stroke();
        t.setLineDash([]); t.strokeStyle = 'rgba(244,143,177,.35)'; t.beginPath(); t.arc(x, y, R * .6, 0, Math.PI * 2); t.stroke();
        [.5, 2.3, 3.9, 5.4].forEach(function (an, i) { var px = x + Math.cos(an) * (R + 4), py = y + Math.sin(an) * (R + 4); t.shadowColor = i % 2 ? '#f48fb1' : '#a78bfa'; t.shadowBlur = 10; t.fillStyle = '#fff'; t.beginPath(); t.arc(px, py, 2.4, 0, Math.PI * 2); t.fill(); });
        t.restore();
    } };
    function donut(id, data, colors) {
        var total = data.reduce(function (a, b) { return a + b; }, 0);
        var grad = function (c) { var a = c.chart.chartArea, col = total ? colors[c.dataIndex] : '#f9a8d0'; if (!a) { return col; } var g = c.chart.ctx.createLinearGradient(a.left, a.top, a.right, a.bottom); g.addColorStop(0, lighten(total ? col : '#c4b5fd')); g.addColorStop(1, col); return g; };
        new Chart(document.getElementById(id), {
            type: 'doughnut',
            data: { datasets: [{ data: total ? data : [1], backgroundColor: grad, borderWidth: 0, borderRadius: total ? 10 : 0, spacing: total ? 4 : 0, hoverOffset: 6 }] },
            options: { maintainAspectRatio: false, cutout: '72%', layout: { padding: 10 }, plugins: { legend: { display: false }, tooltip: { enabled: total > 0, backgroundColor: 'rgba(59,47,99,.94)', cornerRadius: 10, displayColors: false } } },
            plugins: [halo, glow, core]
        });
    }
    donut('chStatus', @json($statusVals), @json($statusCols));
    donut('chKat', @json($katVals), @json($katCols));
})();
</script>
</x-admin-layout>
