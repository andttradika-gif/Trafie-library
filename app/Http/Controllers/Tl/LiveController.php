<?php

namespace App\Http\Controllers\Tl;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

/**
 * Lumi AI (asisten katalog) dan notifikasi lonceng. Memakai tabel yang sudah ada saja.
 */
class LiveController extends Controller
{
    private function bookUrl($b)
    {
        if (Route::has('student.books.show')) {
            return route('student.books.show', $b);
        }

        return '#';
    }

    private function card($b)
    {
        return [
            'title' => (string) $b->title,
            'author' => (string) $b->author,
            'category' => optional($b->category)->name,
            'cover' => $b->cover_url,
            'url' => $this->bookUrl($b),
        ];
    }

    private function has($t, $words)
    {
        foreach ($words as $w) {
            if (mb_strpos($t, $w) !== false) {
                return true;
            }
        }

        return false;
    }

    private function chips()
    {
        return ['Rekomendasi buku seru', 'Buku yang sedang aku pinjam', 'Denda saya', 'Cara meminjam buku'];
    }

    public function lumi(Request $request)
    {
        $q = Str::limit(trim((string) $request->input('q', '')), 200, '');
        if ($q === '') {
            return response()->json(['reply' => 'Tulis pertanyaanmu dulu ya.', 'books' => [], 'chips' => $this->chips()]);
        }
        $t = mb_strtolower($q);
        $user = $request->user();
        $first = $user ? explode(' ', trim((string) $user->name))[0] : 'teman';
        $out = ['reply' => '', 'books' => [], 'chips' => $this->chips()];

        if (mb_strlen($t) < 28 && $this->has($t, ['halo', 'hai', 'hi ', 'hey', 'selamat pagi', 'selamat siang', 'selamat sore', 'selamat malam'])) {
            $out['reply'] = 'Halo ' . $first . '! Aku Lumi, teman membacamu. Mau kucarikan buku, atau cek pinjaman dan dendamu?';

            return response()->json($out);
        }
        if ($this->has($t, ['terima kasih', 'makasih', 'thanks'])) {
            $out['reply'] = 'Sama-sama, ' . $first . '! Selamat membaca ya.';

            return response()->json($out);
        }
        if ($this->has($t, ['cara pinjam', 'cara meminjam', 'bagaimana pinjam', 'gimana pinjam', 'cara perpanjang'])) {
            $out['reply'] = 'Caranya: 1) buka Book Catalog, 2) pilih buku lalu klik pinjam, 3) tunggu persetujuan petugas, 4) ambil bukunya di perpustakaan, 5) kembalikan sebelum jatuh tempo agar tidak kena denda.';

            return response()->json($out);
        }
        if ($user && $this->has($t, ['sedang aku pinjam', 'sedang saya pinjam', 'aku pinjam', 'saya pinjam', 'pinjaman saya', 'pinjamanku', 'jatuh tempo', 'deadline'])) {
            $loans = Loan::with('book')->where('user_id', $user->id)->where('status', 'Borrowed')->orderBy('due_date')->take(5)->get();
            if ($loans->isEmpty()) {
                $out['reply'] = 'Saat ini kamu tidak sedang meminjam buku. Mau kucarikan bacaan baru?';
            } else {
                $lines = [];
                foreach ($loans as $l) {
                    $lines[] = '- ' . optional($l->book)->title . ' (jatuh tempo ' . ($l->due_date ? Carbon::parse($l->due_date)->format('d/m/Y') : '-') . ')';
                }
                $out['reply'] = "Ini buku yang sedang kamu pinjam:\n" . implode("\n", $lines);
            }

            return response()->json($out);
        }
        if ($user && $this->has($t, ['denda'])) {
            $sum = (int) Loan::where('user_id', $user->id)->where('fine_amount', '>', 0)->whereNull('fine_paid_at')->sum('fine_amount');
            $out['reply'] = $sum > 0
                ? 'Total denda yang belum kamu bayar: Rp ' . number_format($sum, 0, ',', '.') . '. Kamu bisa membayar tunai di petugas, lewat e-wallet, atau QRIS.'
                : 'Kamu tidak punya denda yang belum dibayar. Hebat!';

            return response()->json($out);
        }
        if ($this->has($t, ['jam buka', 'jam operasional', 'buka jam'])) {
            $out['reply'] = 'Untuk jam operasional terbaru, silakan tanyakan ke petugas perpustakaan atau lihat pengumuman di dashboard ya.';

            return response()->json($out);
        }

        // ===== pencarian / rekomendasi katalog =====
        $stop = ['yang', 'dan', 'atau', 'di', 'ke', 'dari', 'untuk', 'dengan', 'buku', 'cari', 'carikan', 'tolong', 'dong', 'ya', 'aku', 'saya', 'mau', 'ingin', 'ada', 'apa', 'rekomendasi', 'rekomendasikan', 'saran', 'bacaan', 'tentang', 'genre', 'seru', 'bagus', 'yang', 'lagi', 'baca', 'membaca', 'kamu'];
        $mood = [
            'sedih' => ['romance', 'drama'], 'romantis' => ['romance', 'cinta'], 'cinta' => ['romance', 'cinta'],
            'horor' => ['horor', 'thriller'], 'seram' => ['horor', 'thriller'], 'misteri' => ['mystery', 'misteri'],
            'petualangan' => ['adventure', 'petualangan', 'fantasy'], 'fantasi' => ['fantasy', 'fantasi'], 'sihir' => ['fantasy'],
            'belajar' => ['sains', 'pendidikan'], 'sains' => ['sains'], 'sejarah' => ['sejarah'],
            'motivasi' => ['motivasi', 'self'], 'semangat' => ['motivasi', 'self'], 'anak' => ['anak'],
        ];
        $words = [];
        foreach (preg_split('/[^\p{L}\p{N}]+/u', $t) as $w) {
            if (mb_strlen($w) >= 3 && ! in_array($w, $stop, true)) {
                $words[] = $w;
                if (isset($mood[$w])) {
                    $words = array_merge($words, $mood[$w]);
                }
            }
        }
        $words = array_slice(array_values(array_unique($words)), 0, 8);

        $books = collect();
        if (count($words) > 0) {
            $books = Book::with('category')->where(function ($w) use ($words) {
                foreach ($words as $x) {
                    $w->orWhere('title', 'like', '%' . $x . '%')
                        ->orWhere('author', 'like', '%' . $x . '%')
                        ->orWhere('description', 'like', '%' . $x . '%')
                        ->orWhereHas('category', function ($c) use ($x) {
                            $c->where('name', 'like', '%' . $x . '%');
                        });
                }
            })->orderByDesc('id')->take(4)->get();
        }
        if ($books->isEmpty()) {
            $books = Book::with('category')->inRandomOrder()->take(3)->get();
            $out['reply'] = $books->isEmpty()
                ? 'Katalog masih kosong. Minta admin menambahkan buku dulu ya.'
                : 'Aku belum menemukan yang persis cocok, tapi mungkin kamu suka buku-buku ini:';
        } else {
            $out['reply'] = 'Aku menemukan ' . $books->count() . ' buku yang cocok untukmu:';
        }
        foreach ($books as $b) {
            $out['books'][] = $this->card($b);
        }

        return response()->json($out);
    }

    public function notifs(Request $request)
    {
        $u = $request->user();
        $items = [];
        $warn = 0;
        if ($u && $u->role === 'student') {
            $loans = Loan::with('book')->where('user_id', $u->id)->where('status', 'Borrowed')->whereNotNull('due_date')->orderBy('due_date')->get();
            foreach ($loans as $l) {
                $diff = (int) round(today()->diffInDays(Carbon::parse($l->due_date)->startOfDay(), false));
                $title = (string) optional($l->book)->title;
                if ($diff < 0) {
                    $items[] = ['level' => 'warn', 'title' => 'Terlambat ' . abs($diff) . ' hari', 'body' => $title];
                    $warn++;
                } elseif ($diff <= 2) {
                    $items[] = ['level' => 'warn', 'title' => $diff === 0 ? 'Jatuh tempo hari ini' : 'Jatuh tempo ' . $diff . ' hari lagi', 'body' => $title];
                    $warn++;
                }
            }
            $sum = (int) Loan::where('user_id', $u->id)->where('fine_amount', '>', 0)->whereNull('fine_paid_at')->sum('fine_amount');
            if ($sum > 0) {
                $items[] = ['level' => 'warn', 'title' => 'Denda belum dibayar', 'body' => 'Total Rp ' . number_format($sum, 0, ',', '.')];
                $warn++;
            }
            $recent = Loan::with('book')->where('user_id', $u->id)->whereIn('status', ['Borrowed', 'Returned', 'Rejected'])->where('updated_at', '>=', now()->subDays(3))->latest('updated_at')->take(3)->get();
            $label = ['Borrowed' => 'Peminjaman disetujui', 'Returned' => 'Buku sudah dikembalikan', 'Rejected' => 'Permintaan pinjam ditolak'];
            foreach ($recent as $l) {
                $items[] = ['level' => $l->status === 'Rejected' ? 'warn' : 'ok', 'title' => $label[$l->status], 'body' => (string) optional($l->book)->title];
            }
        } else {
            $pending = Loan::with(['user', 'book'])->where('status', 'Pending')->latest('id')->take(4)->get();
            foreach ($pending as $l) {
                $items[] = ['level' => 'info', 'title' => 'Permintaan pinjam baru', 'body' => optional($l->user)->name . ' - ' . optional($l->book)->title];
            }
            $warn = (int) Loan::where('status', 'Pending')->count();
            $late = (int) Loan::where('status', 'Borrowed')->whereDate('due_date', '<', today())->count();
            if ($late > 0) {
                $items[] = ['level' => 'warn', 'title' => $late . ' buku terlambat', 'body' => 'Perlu ditagih ke siswa.'];
                $warn += $late;
            }
            $unpaid = (int) Loan::where('fine_amount', '>', 0)->whereNull('fine_paid_at')->count();
            if ($unpaid > 0) {
                $items[] = ['level' => 'info', 'title' => $unpaid . ' denda belum dibayar', 'body' => 'Cek menu Denda / Pembayaran.'];
            }
        }

        return response()->json(['count' => $warn, 'items' => array_slice($items, 0, 8)]);
    }
}