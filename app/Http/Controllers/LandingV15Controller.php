<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class LandingV15Controller extends Controller
{
    private function pick(string $table, array $cands): ?string
    {
        foreach ($cands as $c) {
            if (Schema::hasColumn($table, $c)) return $c;
        }
        return null;
    }

    private function coverUrl($v): ?string
    {
        if (!$v) return null;
        if (preg_match('#^https?://#', $v)) return $v;
        $v = ltrim($v, '/');
        if (file_exists(public_path($v))) return asset($v);
        return asset('storage/' . $v);
    }

    private function firstRoute(array $names, string $fallback): string
    {
        foreach ($names as $n) {
            if (Route::has($n)) return route($n);
        }
        return url($fallback);
    }

    public function index()
    {
        $books = [];
        $reviews = [];
        $stats = ['books' => 0, 'students' => 0, 'loans' => 0, 'satisfaction' => null];

        try {
            // ---- rating dari ulasan yang sudah disetujui
            $ratingByBook = [];
            $reviewsOk = false;
            if (Schema::hasTable('reviews') && Schema::hasColumn('reviews', 'rating')) {
                $q = DB::table('reviews');
                if (Schema::hasColumn('reviews', 'status')) {
                    $q->where('status', 'approved');
                    $reviewsOk = true;
                } elseif (Schema::hasColumn('reviews', 'is_approved')) {
                    $q->where('is_approved', 1);
                    $reviewsOk = true;
                }
                if ($reviewsOk) {
                    $all = $q->get();
                    if ($all->count() > 0) {
                        $stats['satisfaction'] = (int) round($all->avg('rating') / 5 * 100);
                    }
                    if (Schema::hasColumn('reviews', 'book_id')) {
                        foreach ($all->groupBy('book_id') as $bid => $grp) {
                            $ratingByBook[$bid] = ['avg' => round($grp->avg('rating'), 1), 'n' => $grp->count()];
                        }
                    }
                    $textCol = $this->pick('reviews', ['comment', 'body', 'review', 'content', 'ulasan']);
                    foreach ($all->sortByDesc('id')->take(3) as $r) {
                        $name = 'Pembaca';
                        if (Schema::hasColumn('reviews', 'user_id') && Schema::hasTable('users')) {
                            $u = DB::table('users')->where('id', $r->user_id)->first();
                            if ($u && isset($u->name)) $name = $u->name;
                        }
                        $reviews[] = [
                            'name' => $name,
                            'text' => $textCol ? ($r->$textCol ?? '') : '',
                            'rating' => (int) $r->rating,
                        ];
                    }
                }
            }

            // ---- ulasan dari book_reviews (rating langsung tampil)
            if (Schema::hasTable('book_reviews')) {
                $br = \App\Models\BookReview::with('user')->whereNotNull('approved_at')->get();
                $reviews = [];
                $ratingByBook = [];
                if ($br->count() > 0) {
                    $stats['satisfaction'] = (int) round($br->avg('rating') / 5 * 100);
                    foreach ($br->groupBy('book_id') as $bid => $grp) {
                        $ratingByBook[$bid] = ['avg' => round($grp->avg('rating'), 1), 'n' => $grp->count()];
                    }
                    foreach ($br->sortByDesc('id')->take(3) as $r) {
                        $reviews[] = [
                            'name' => $r->user->name ?? 'Pembaca',
                            'text' => trim((string) $r->body) !== '' ? $r->body : 'Memberi rating ' . $r->rating . ' bintang.',
                            'rating' => (int) $r->rating,
                        ];
                    }
                }
            }

            // ---- buku
            if (Schema::hasTable('books')) {
                $t = $this->pick('books', ['title', 'judul', 'name']);
                $a = $this->pick('books', ['author', 'penulis']);
                $c = $this->pick('books', ['cover', 'cover_path', 'cover_image', 'image', 'gambar', 'thumbnail']);
                $k = $this->pick('books', ['category', 'kategori', 'genre']);
                $stats['books'] = DB::table('books')->count();
                foreach (DB::table('books')->orderByDesc('id')->limit(10)->get() as $b) {
                    $kat = $k ? $b->$k : null;
                    $books[] = [
                        'title' => $t ? $b->$t : 'Tanpa judul',
                        'author' => $a ? $b->$a : '',
                        'cover' => $c ? $this->coverUrl($b->$c) : null,
                        'category' => is_scalar($kat) && !is_numeric($kat) ? $kat : null,
                        'rating' => $ratingByBook[$b->id] ?? null,
                    ];
                }
            }

            // ---- siswa & peminjaman
            if (Schema::hasTable('users')) {
                $q = DB::table('users');
                if (Schema::hasColumn('users', 'role')) {
                    $q->whereIn('role', ['siswa', 'student', 'user', 'member']);
                }
                $stats['students'] = $q->count();
            }
            if (Schema::hasTable('loans')) {
                $dc = $this->pick('loans', ['created_at', 'loan_date', 'borrowed_at']);
                $q = DB::table('loans');
                if ($dc) $q->whereMonth($dc, now()->month)->whereYear($dc, now()->year);
                $stats['loans'] = $q->count();
            }
        } catch (\Throwable $e) {
            // landing tidak boleh error walau struktur tabel berbeda
        }

        $catalog = $this->firstRoute(['catalog.index', 'katalog.index', 'books.index', 'student.books.index', 'siswa.books.index', 'katalog'], '/books');
        $dashboard = $this->firstRoute(['dashboard'], '/dashboard');

        return view('landing-trafie', [
            'books' => $books,
            'reviews' => $reviews,
            'stats' => $stats,
            'catalog' => $catalog,
            'dashboard' => $dashboard,
            'loggedIn' => Auth::check(),
            'loginUrl' => url('/login'),
            'registerUrl' => url('/register'),
        ]);
    }
}