<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Halaman admin tambahan (V2-33): Eksemplar, Denda, Pembayaran, Pengembalian, Kalender, Log Aktivitas.
 * Hanya memakai kolom yang sudah ada di tabel loans / books / book_copies.
 */
class AdminExtraController extends Controller
{
    public function copies(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $query = Book::withCount([
            'copies',
            'copies as available_count' => fn ($c) => $c->where('status', 'Available'),
        ])->orderBy('title');
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', '%' . $q . '%')->orWhere('author', 'like', '%' . $q . '%');
            });
        }

        return view('admin.extra.copies', [
            'rows' => $query->simplePaginate(12)->withQueryString(),
            'q' => $q,
            'totalCopies' => BookCopy::count(),
            'available' => BookCopy::where('status', 'Available')->count(),
            'totalJudul' => Book::count(),
        ]);
    }

    public function fines(Request $request)
    {
        $s = (string) $request->query('s', 'all');
        $query = Loan::with(['user', 'book'])->where('fine_amount', '>', 0)->latest('id');
        if ($s === 'unpaid') {
            $query->whereNull('fine_paid_at');
        } elseif ($s === 'paid') {
            $query->whereNotNull('fine_paid_at');
        } else {
            $s = 'all';
        }

        return view('admin.extra.fines', [
            'rows' => $query->simplePaginate(12)->withQueryString(),
            's' => $s,
            'total' => Loan::sum('fine_amount'),
            'paid' => Loan::whereNotNull('fine_paid_at')->sum('fine_amount'),
            'unpaid' => Loan::where('fine_amount', '>', 0)->whereNull('fine_paid_at')->sum('fine_amount'),
        ]);
    }

    public function payFine(Loan $loan)
    {
        if ((int) $loan->fine_amount > 0 && $loan->fine_paid_at === null) {
            $loan->fine_paid_at = now();
            $loan->save();

            return back()->with('success', 'Denda ditandai lunas.');
        }

        return back()->with('error', 'Denda ini tidak perlu ditandai lunas.');
    }

    public function payments()
    {
        $rows = Loan::with(['user', 'book'])->whereNotNull('fine_paid_at')->orderByDesc('fine_paid_at')->simplePaginate(12);

        return view('admin.extra.payments', [
            'rows' => $rows,
            'total' => Loan::whereNotNull('fine_paid_at')->sum('fine_amount'),
            'count' => Loan::whereNotNull('fine_paid_at')->count(),
        ]);
    }

    public function returns()
    {
        $late = Loan::with(['user', 'book'])->where('status', 'Borrowed')->whereDate('due_date', '<', today())->orderBy('due_date')->take(8)->get();
        $rows = Loan::with(['user', 'book'])->where('status', 'Returned')->latest('updated_at')->simplePaginate(12);

        return view('admin.extra.returns', [
            'late' => $late,
            'rows' => $rows,
            'totalReturned' => Loan::where('status', 'Returned')->count(),
            'totalLate' => Loan::where('status', 'Borrowed')->whereDate('due_date', '<', today())->count(),
        ]);
    }

    public function calendar(Request $request)
    {
        $m = (string) $request->query('m', '');
        try {
            $cur = $m !== '' ? Carbon::createFromFormat('!Y-m', $m)->startOfMonth() : now()->startOfMonth();
        } catch (\Throwable $e) {
            $cur = now()->startOfMonth();
        }

        $loans = Loan::with(['user', 'book'])->where('status', 'Borrowed')
            ->whereYear('due_date', $cur->year)->whereMonth('due_date', $cur->month)->get();
        $byDay = [];
        foreach ($loans as $l) {
            $byDay[(int) Carbon::parse($l->due_date)->day][] = $l;
        }

        return view('admin.extra.calendar', [
            'cur' => $cur,
            'byDay' => $byDay,
            'prev' => $cur->copy()->subMonth()->format('Y-m'),
            'next' => $cur->copy()->addMonth()->format('Y-m'),
            'jumlah' => $loans->count(),
        ]);
    }

    public function activity()
    {
        return view('admin.extra.activity', [
            'rows' => Loan::with(['user', 'book'])->latest('updated_at')->simplePaginate(15),
        ]);
    }
}