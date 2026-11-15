<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    private const MAX_ACTIVE = 3;

    public function index(Request $request)
    {
        $loans = Loan::with('book.category')
            ->where('user_id', $request->user()->id)
            ->latest('id')
            ->paginate(10);

        $active = Loan::where('user_id', $request->user()->id)->whereIn('status', ['Pending', 'Borrowed'])->count();

        return view('student.loans.index', ['loans' => $loans, 'active' => $active, 'max' => self::MAX_ACTIVE]);
    }

    public function store(Request $request, Book $book)
    {
        $data = $request->validate(['duration_days' => 'required|in:3,7,10']);
        $user = $request->user();

        $active = Loan::where('user_id', $user->id)->whereIn('status', ['Pending', 'Borrowed']);

        if ((clone $active)->where('book_id', $book->id)->exists()) {
            return back()->with('error', 'Kamu sudah meminjam atau mengajukan buku ini.');
        }
        if ((clone $active)->count() >= self::MAX_ACTIVE) {
            return back()->with('error', 'Batas maksimal ' . self::MAX_ACTIVE . ' peminjaman aktif sudah tercapai.');
        }
        if (!$book->copies()->where('status', 'Available')->exists()) {
            return back()->with('error', 'Semua eksemplar buku ini sedang dipinjam.');
        }

        Loan::create([
            'user_id'       => $user->id,
            'book_id'       => $book->id,
            'duration_days' => (int) $data['duration_days'],
            'status'        => 'Pending',
            'requested_at'  => now(),
        ]);

        return redirect()->route('student.loans.index')
            ->with('success', 'Permintaan pinjam dikirim. Tunggu persetujuan admin.');
    }

    public function cancel(Request $request, Loan $loan)
    {
        abort_if($loan->user_id !== $request->user()->id, 403);

        if ($loan->status !== 'Pending') {
            return back()->with('error', 'Hanya permintaan yang masih menunggu yang bisa dibatalkan.');
        }

        $loan->update(['status' => 'Cancelled']);
        return back()->with('success', 'Permintaan dibatalkan.');
    }
}