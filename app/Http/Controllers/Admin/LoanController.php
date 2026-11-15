<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookCopy;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        $status = (string) $request->query('status', '');
        if (!array_key_exists($status, Loan::LABELS)) {
            $status = '';
        }

        $loans = Loan::with(['user', 'book', 'copy'])
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $counts = Loan::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.loans.index', compact('loans', 'status', 'counts'));
    }

    public function approve(Request $request, Loan $loan)
    {
        $adminId = $request->user()->id;

        $error = DB::transaction(function () use ($loan, $adminId) {
            $loan = Loan::lockForUpdate()->find($loan->id);
            if (!$loan || $loan->status !== 'Pending') {
                return 'Permintaan ini sudah diproses.';
            }

            $copy = null;
            if ($loan->book_copy_id) {
                $copy = BookCopy::where('id', $loan->book_copy_id)->where('status', 'Reserved')->lockForUpdate()->first();
            }
            if (!$copy) {
                $copy = BookCopy::where('book_id', $loan->book_id)->where('status', 'Available')->lockForUpdate()->first();
            }
            if (!$copy) {
                return 'Tidak ada eksemplar tersedia untuk buku ini.';
            }

            $copy->update(['status' => 'Borrowed']);
            \App\Models\Reservation::where('book_copy_id', $copy->id)->where('status', 'Claimed')->update(['status' => 'Fulfilled']);
            $loan->update([
                'book_copy_id' => $copy->id,
                'status'       => 'Borrowed',
                'approved_at'  => now(),
                'approved_by'  => $adminId,
                'due_date'     => now()->addDays($loan->duration_days)->toDateString(),
            ]);
            return null;
        });

        return $error ? back()->with('error', $error) : back()->with('success', 'Peminjaman disetujui.');
    }

    public function reject(Loan $loan)
    {
        if ($loan->status !== 'Pending') {
            return back()->with('error', 'Permintaan ini sudah diproses.');
        }
        $loan->update(['status' => 'Rejected']);
        return back()->with('success', 'Permintaan ditolak.');
    }

    public function returnBook(Loan $loan)
    {
        $result = DB::transaction(function () use ($loan) {
            $loan = Loan::lockForUpdate()->find($loan->id);
            if (!$loan || $loan->status !== 'Borrowed') {
                return ['error' => 'Buku ini tidak sedang dipinjam.'];
            }

            // isi dulu status dan waktu kembali di memori supaya hitungan telat memakai waktu kembali
            $loan->status = 'Returned';
            $loan->returned_at = now();
            $loan->fine_amount = $loan->late_days * (int) config('library.fine_per_day');
            $loan->save();

            if ($loan->book_copy_id) {
                BookCopy::where('id', $loan->book_copy_id)->update(['status' => 'Available']);
                \App\Services\Reservations::promote((int) $loan->book_id);
            }
            return ['fine' => $loan->fine_amount, 'days' => $loan->late_days];
        });

        if (isset($result['error'])) {
            return back()->with('error', $result['error']);
        }

        $msg = 'Buku dikembalikan dan eksemplar tersedia lagi.';
        if ($result['fine'] > 0) {
            $msg .= ' Terlambat ' . $result['days'] . ' hari, denda ' . Loan::rupiah($result['fine']) . '.';
        }
        return back()->with('success', $msg);
    }

    public function finePaid(Loan $loan)
    {
        if ($loan->status !== 'Returned' || $loan->fine_amount <= 0 || $loan->fine_paid_at) {
            return back()->with('error', 'Tidak ada denda yang perlu ditandai lunas.');
        }
        $loan->update(['fine_paid_at' => now()]);
        return back()->with('success', 'Denda ' . Loan::rupiah($loan->fine_amount) . ' ditandai lunas.');
    }
}