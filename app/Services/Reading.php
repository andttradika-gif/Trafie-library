<?php

namespace App\Services;

use App\Models\Book;
use App\Models\BookReview;
use App\Models\Loan;
use App\Models\ReadingChallenge;
use App\Models\ReadingCompletion;
use App\Models\ReadingLog;
use App\Models\ReadingProgress;
use App\Models\User;
use App\Models\UserAchievement;
use App\Models\XpTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Reading
{
    public const LEVELS = [
        [1, 'Baby Reader', 0], [2, 'Book Explorer', 200], [3, 'Story Seeker', 500],
        [4, 'Dream Reader', 1000], [5, 'Star Reader', 2000], [6, 'Galaxy Reader', 5000],
    ];

    // kode => [nama, deskripsi, metrik, target, ikon]
    public const ACH = [
        'first_bloom'     => ['First Bloom', 'Selesaikan buku pertamamu', 'books', 1, 'heart'],
        'bookworm'        => ['Bookworm', 'Selesaikan 5 buku', 'books', 5, 'book'],
        'reading_star'    => ['Reading Star', 'Selesaikan 10 buku', 'books', 10, 'star'],
        'bibliophile'     => ['Bibliophile', 'Selesaikan 20 buku', 'books', 20, 'layers'],
        'genre_explorer'  => ['Genre Explorer', 'Selesaikan buku dari 3 genre berbeda', 'genres', 3, 'compass'],
        'review_fairy'    => ['Review Fairy', 'Tulis 5 ulasan', 'reviews', 5, 'gift'],
        'reading_streak'  => ['Reading Streak', 'Baca 7 hari berturut-turut', 'streak', 7, 'trend'],
        'galaxy_guardian' => ['Galaxy Guardian', 'Kumpulkan 5000 XP', 'xp', 5000, 'award'],
    ];

    public static function icon(string $k): string
    {
        $m = [
            'heart' => '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 0 0 0-7.8z"/>',
            'book' => '<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>',
            'star' => '<path d="M12 2l3 6.5 7 .9-5.1 4.8 1.3 7L12 17.8 5.8 21.2l1.3-7L2 9.4l7-.9z"/>',
            'layers' => '<path d="M12 2l10 5-10 5L2 7z"/><path d="M2 12l10 5 10-5"/><path d="M2 17l10 5 10-5"/>',
            'compass' => '<circle cx="12" cy="12" r="9"/><path d="M16 8l-2 6-6 2 2-6z"/>',
            'gift' => '<path d="M20 4C9 4 4 9 4 16v4h4c7 0 12-5 12-16z"/><path d="M4 20L14 10"/>',
            'trend' => '<path d="M22 7l-8.5 8.5-5-5L2 17"/><path d="M16 7h6v6"/>',
            'award' => '<circle cx="12" cy="8" r="6"/><path d="M8.2 13.5L7 22l5-3 5 3-1.2-8.5"/>',
        ];
        return $m[$k] ?? $m['star'];
    }

    public static function xp(int $uid): int
    {
        return (int) XpTransaction::where('user_id', $uid)->sum('amount');
    }

    public static function levelFor(int $xp): array
    {
        $cur = self::LEVELS[0];
        $next = null;
        foreach (self::LEVELS as $i => $l) {
            if ($xp >= $l[2]) { $cur = $l; $next = self::LEVELS[$i + 1] ?? null; }
        }
        $pct = $next ? (int) round(($xp - $cur[2]) / ($next[2] - $cur[2]) * 100) : 100;
        return ['level' => $cur[0], 'name' => $cur[1], 'min' => $cur[2], 'next' => $next, 'pct' => max(0, min(100, $pct)), 'to_next' => $next ? max(0, $next[2] - $xp) : 0];
    }

    public static function award(User $u, int $amount, string $reason, string $ref, ?string $note = null): int
    {
        if ($amount === 0) { return 0; }
        $before = self::levelFor(self::xp($u->id))['level'];
        $row = XpTransaction::firstOrCreate(['user_id' => $u->id, 'reason' => $reason, 'ref' => $ref], ['amount' => $amount, 'note' => $note]);
        if (!$row->wasRecentlyCreated) { return 0; }
        $after = self::levelFor(self::xp($u->id));
        if ($after['level'] > $before) {
            Notice::to($u, 'xp', 'Level naik!', 'Selamat, kamu sekarang Level ' . $after['level'] . ' (' . $after['name'] . ').', route('student.achievements.index', [], false), 'star');
        }
        return $amount;
    }

    public static function streaks(int $uid): array
    {
        $days = ReadingLog::where('user_id', $uid)->pluck('log_date')->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->merge(ReadingCompletion::where('user_id', $uid)->where('status', 'Verified')->pluck('completed_at')->map(fn ($d) => Carbon::parse($d)->toDateString()))
            ->unique()->sort()->values();
        if ($days->isEmpty()) { return [0, 0]; }

        $best = 1; $run = 1;
        for ($i = 1; $i < $days->count(); $i++) {
            $gap = abs((int) Carbon::parse($days[$i - 1])->diffInDays(Carbon::parse($days[$i])));
            $run = $gap === 1 ? $run + 1 : 1;
            $best = max($best, $run);
        }

        $set = $days->flip();
        $cursor = today();
        if (!isset($set[$cursor->toDateString()])) { $cursor = $cursor->copy()->subDay(); }
        $cur = 0;
        while (isset($set[$cursor->toDateString()])) { $cur++; $cursor = $cursor->copy()->subDay(); }
        return [$cur, $best];
    }

    public static function metrics(int $uid): array
    {
        $books = ReadingCompletion::where('user_id', $uid)->where('status', 'Verified')->count();
        $genres = (int) DB::table('reading_completions as rc')->join('books as b', 'b.id', '=', 'rc.book_id')
            ->where('rc.user_id', $uid)->where('rc.status', 'Verified')->distinct()->count('b.category_id');
        [$cur, $best] = self::streaks($uid);
        return ['books' => $books, 'genres' => $genres, 'reviews' => BookReview::where('user_id', $uid)->count(), 'xp' => self::xp($uid), 'streak' => max($cur, $best)];
    }

    public static function evaluateAchievements(User $u): array
    {
        $m = self::metrics($u->id);
        $have = UserAchievement::where('user_id', $u->id)->pluck('code')->all();
        $new = [];
        foreach (self::ACH as $code => $a) {
            if (in_array($code, $have, true)) { continue; }
            if (($m[$a[2]] ?? 0) >= $a[3]) {
                UserAchievement::firstOrCreate(['user_id' => $u->id, 'code' => $code], ['unlocked_at' => now()]);
                Notice::to($u, 'xp', 'Achievement terbuka: ' . $a[0], $a[1], route('student.achievements.index', [], false), 'star');
                $new[] = $code;
            }
        }
        return $new;
    }

    public static function challengeCount(int $uid, ReadingChallenge $c, $joinedAt): int
    {
        $from = Carbon::parse($joinedAt ?: $c->starts_on)->startOfDay();
        $start = $c->starts_on->copy()->startOfDay();
        if ($start->greaterThan($from)) { $from = $start; }
        $to = $c->ends_on->copy()->endOfDay();
        $q = DB::table('reading_completions as rc')->join('books as b', 'b.id', '=', 'rc.book_id')
            ->where('rc.user_id', $uid)->where('rc.status', 'Verified')->whereBetween('rc.completed_at', [$from, $to]);
        if ($c->category_id) { $q->where('b.category_id', $c->category_id); }
        return (int) $q->count();
    }

    public static function evaluateChallenges(User $u): void
    {
        $rows = DB::table('challenge_participants')->where('user_id', $u->id)->whereNull('completed_at')->get();
        foreach ($rows as $row) {
            $c = ReadingChallenge::find($row->challenge_id);
            if (!$c || $c->status !== 'Active') { continue; }
            if (self::challengeCount($u->id, $c, $row->joined_at) >= $c->target_books) {
                DB::table('challenge_participants')->where('id', $row->id)->update(['completed_at' => now(), 'updated_at' => now()]);
                self::award($u, (int) $c->reward_xp, 'challenge', 'challenge:' . $c->id, $c->title);
                Notice::to($u, 'xp', 'Tantangan selesai', 'Kamu menyelesaikan "' . $c->title . '"' . ($c->badge_name ? ' dan mendapat badge ' . $c->badge_name : '') . '.', route('student.challenges.index', [], false), 'star');
            }
        }
    }

    // Verifikasi penyelesaian: harus pernah meminjam, sudah dikembalikan, sudah mengajukan, hanya sekali per buku
    public static function complete(User $u, int $bookId, int $rating, string $reflection): array
    {
        return DB::transaction(function () use ($u, $bookId, $rating, $reflection) {
            $p = ReadingProgress::where('user_id', $u->id)->where('book_id', $bookId)->lockForUpdate()->first();
            if (!$p) { return [false, 'Catat progres membaca buku ini dulu.']; }
            if (ReadingCompletion::where('user_id', $u->id)->where('book_id', $bookId)->exists()) {
                return [false, 'Buku ini sudah pernah diselesaikan. XP hanya diberikan satu kali.'];
            }
            if (!$p->completion_requested_at) { return [false, 'Ajukan penyelesaian buku dulu.']; }
            $loan = Loan::where('user_id', $u->id)->where('book_id', $bookId)->where('status', 'Returned')->latest('returned_at')->first();
            if (!$loan) { return [false, 'Buku harus pernah kamu pinjam dan sudah dikembalikan ke perpustakaan.']; }
            $book = Book::find($bookId);
            if (!$book) { return [false, 'Buku tidak ditemukan.']; }

            ReadingCompletion::create([
                'user_id' => $u->id, 'book_id' => $bookId, 'loan_id' => $loan->id, 'rating' => $rating,
                'reflection' => $reflection, 'completed_at' => now(), 'status' => 'Verified',
            ]);
            $p->update(['status' => 'Completed', 'current_page' => $p->total_pages ?: $p->current_page, 'last_activity_at' => now()]);

            $ref = 'book:' . $bookId;
            $xp = self::award($u, 100, 'completion', $ref, $book->title);

            if (!BookReview::where('user_id', $u->id)->where('book_id', $bookId)->exists()) {
                BookReview::create(['user_id' => $u->id, 'book_id' => $bookId, 'rating' => $rating, 'body' => $reflection]);
                $xp += self::award($u, 20, 'review', $ref, $book->title);
                $xp += self::award($u, 10, 'rating', $ref, $book->title);
            }

            $inGenre = DB::table('reading_completions as rc')->join('books as b', 'b.id', '=', 'rc.book_id')
                ->where('rc.user_id', $u->id)->where('rc.status', 'Verified')->where('b.category_id', $book->category_id)->count();
            if ($inGenre === 1) { $xp += self::award($u, 25, 'new_genre', $ref, 'Genre baru'); }

            self::evaluateAchievements($u);
            self::evaluateChallenges($u);
            return [true, 'Buku terverifikasi selesai! Kamu mendapat +' . $xp . ' XP dan bintang baru di Reading Galaxy.'];
        });
    }

    public static function revoke(ReadingCompletion $c): void
    {
        DB::transaction(function () use ($c) {
            $c->update(['status' => 'Revoked', 'revoked_at' => now()]);
            foreach (XpTransaction::where('user_id', $c->user_id)->where('ref', 'book:' . $c->book_id)->where('amount', '>', 0)->get() as $tx) {
                XpTransaction::firstOrCreate(['user_id' => $c->user_id, 'reason' => 'revoke', 'ref' => 'tx:' . $tx->id], ['amount' => -$tx->amount, 'note' => 'Dibatalkan admin']);
            }
            ReadingProgress::where('user_id', $c->user_id)->where('book_id', $c->book_id)->update(['status' => 'Reading', 'completion_requested_at' => null]);
        });
    }

    public static function summary(User $u): array
    {
        $xp = self::xp($u->id);
        [$cur] = self::streaks($u->id);
        return [
            'xp' => $xp, 'lv' => self::levelFor($xp),
            'books' => ReadingCompletion::where('user_id', $u->id)->where('status', 'Verified')->count(),
            'reading' => ReadingProgress::where('user_id', $u->id)->where('status', 'Reading')->count(),
            'streak' => $cur,
        ];
    }
}