<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use App\Models\Reservation;
use App\Models\UserSetting;
use App\Services\Reservations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    private const MAX_ACTIVE = 3;

    public function index(Request $request)
    {
        Reservations::expireDue();
        $uid = $request->user()->id;

        $mine = Reservation::with(['book', 'copy'])->where('user_id', $uid)
            ->orderByRaw("FIELD(status, 'Ready', 'Waiting', 'Claimed', 'Fulfilled', 'Expired', 'Cancelled')")
            ->latest('id')->get();

        $pos = [];
        $cnt = [];
        foreach (Reservation::where('status', 'Waiting')->orderBy('id')->get(['id', 'book_id']) as $w) {
            $cnt[$w->book_id] = ($cnt[$w->book_id] ?? 0) + 1;
            $pos[$w->id] = $cnt[$w->book_id];
        }

        $busy = Reservation::where('user_id', $uid)->whereIn('status', ['Waiting', 'Ready', 'Claimed'])->pluck('book_id')
            ->merge(Loan::where('user_id', $uid)->whereIn('status', ['Pending', 'Borrowed'])->pluck('book_id'))
            ->unique()->all();

        $q = trim((string) $request->query('q', ''));
        $books = Book::with('category')
            ->withCount(['copies', 'copies as available_count' => fn ($c) => $c->where('status', 'Available')])
            ->when($q !== '', fn ($x) => $x->where(fn ($w) => $w->where('title', 'like', "%$q%")->orWhere('author', 'like', "%$q%")))
            ->whereNotIn('id', $busy)
            ->having('available_count', '=', 0)->having('copies_count', '>', 0)
            ->orderBy('title')->take(24)->get();

        $queue = Reservation::where('status', 'Waiting')->selectRaw('book_id, count(*) as c')->groupBy('book_id')->pluck('c', 'book_id');
        $defaultDur = UserSetting::forUser($uid)->default_duration;

        return view('student.reservations.index', compact('mine', 'pos', 'books', 'queue', 'q', 'defaultDur'));
    }

    public function store(Request $request, Book $book)
    {
        $uid = $request->user()->id;

        $available = $book->copies()->where('status', 'Available')->exists();
        if ($available) { return back()->with('error', 'Buku ini masih tersedia. Langsung ajukan pinjam dari katalog.'); }
        if (!$book->copies()->exists()) { return back()->with('error', 'Buku ini belum punya eksemplar.'); }
        if (Loan::where('user_id', $uid)->where('book_id', $book->id)->whereIn('status', ['Pending', 'Borrowed'])->exists()) {
            return back()->with('error', 'Kamu sedang meminjam atau mengajukan pinjam buku ini.');
        }
        if (Reservation::where('user_id', $uid)->where('book_id', $book->id)->whereIn('status', ['Waiting', 'Ready', 'Claimed'])->exists()) {
            return back()->with('error', 'Kamu sudah ada di antrean buku ini.');
        }
        if (Reservation::where('user_id', $uid)->whereIn('status', ['Waiting', 'Ready', 'Claimed'])->count() >= self::MAX_ACTIVE) {
            return back()->with('error', 'Maksimal ' . self::MAX_ACTIVE . ' reservasi aktif sekaligus.');
        }

        Reservation::create(['user_id' => $uid, 'book_id' => $book->id, 'status' => 'Waiting']);
        $posisi = Reservation::where('book_id', $book->id)->where('status', 'Waiting')->count();
        return back()->with('success', 'Kamu masuk antrean "' . $book->title . '" di posisi #' . $posisi . '.');
    }

    public function cancel(Request $request, Reservation $reservation)
    {
        abort_unless($reservation->user_id === $request->user()->id, 403);
        $error = Reservations::cancel($reservation);
        return $error ? back()->with('error', $error) : back()->with('success', 'Reservasi dibatalkan.');
    }

    public function claim(Request $request, Reservation $reservation)
    {
        $uid = $request->user()->id;
        abort_unless($reservation->user_id === $uid, 403);
        $data = $request->validate(['duration' => 'required|in:3,7,10']);

        Reservations::expireDue();
        $limit = (int) config('library.max_loans', 3);

        $error = DB::transaction(function () use ($reservation, $uid, $data, $limit) {
            $r = Reservation::lockForUpdate()->find($reservation->id);
            if (!$r || $r->status !== 'Ready' || !$r->book_copy_id) { return 'Reservasi ini sudah tidak bisa diambil.'; }
            if ($r->expires_at && $r->expires_at->isPast()) { return 'Batas ambil sudah lewat.'; }
            if (Loan::where('user_id', $uid)->whereIn('status', ['Pending', 'Borrowed'])->count() >= $limit) {
                return 'Batas peminjaman aktif (' . $limit . ' buku) tercapai. Kembalikan buku dulu.';
            }
            Loan::create([
                'user_id' => $uid, 'book_id' => $r->book_id, 'book_copy_id' => $r->book_copy_id,
                'duration_days' => (int) $data['duration'], 'status' => 'Pending', 'requested_at' => now(),
            ]);
            $r->update(['status' => 'Claimed']);
            return null;
        });

        return $error ? back()->with('error', $error) : redirect()->route('student.loans.index')->with('success', 'Pengajuan pinjam dikirim ke admin. Eksemplarmu tetap ditahan.');
    }
}