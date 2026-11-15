<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanRenewal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RenewalController extends Controller
{
    public function index()
    {
        $pending = LoanRenewal::with(['loan.user', 'loan.book'])
            ->where('status', 'Pending')->oldest('id')->get();

        $history = LoanRenewal::with(['loan.user', 'loan.book'])
            ->whereIn('status', ['Approved', 'Rejected'])->latest('id')->paginate(10);

        return view('admin.renewals.index', compact('pending', 'history'));
    }

    public function approve(Request $request, LoanRenewal $renewal)
    {
        $adminId = $request->user()->id;

        $error = DB::transaction(function () use ($renewal, $adminId) {
            $renewal = LoanRenewal::lockForUpdate()->find($renewal->id);
            if (!$renewal || $renewal->status !== 'Pending') {
                return 'Permintaan ini sudah diproses.';
            }

            $loan = Loan::lockForUpdate()->find($renewal->loan_id);
            if (!$loan || $loan->status !== 'Borrowed') {
                return 'Buku ini tidak sedang dipinjam.';
            }
            if ($loan->isOverdue()) {
                return 'Pinjaman sudah lewat jatuh tempo, perpanjangan tidak bisa disetujui. Tolak, atau terima buku kembali.';
            }
            if ($loan->renewals()->where('status', 'Approved')->exists()) {
                return 'Pinjaman ini sudah pernah diperpanjang.';
            }

            $loan->update(['due_date' => $loan->due_date->copy()->addDays($renewal->extra_days)->toDateString()]);
            $renewal->update(['status' => 'Approved', 'decided_by' => $adminId, 'decided_at' => now()]);
            return null;
        });

        return $error ? back()->with('error', $error) : back()->with('success', 'Perpanjangan disetujui, jatuh tempo diperbarui.');
    }

    public function reject(Request $request, LoanRenewal $renewal)
    {
        if ($renewal->status !== 'Pending') {
            return back()->with('error', 'Permintaan ini sudah diproses.');
        }
        $renewal->update(['status' => 'Rejected', 'decided_by' => $request->user()->id, 'decided_at' => now()]);
        return back()->with('success', 'Perpanjangan ditolak.');
    }
}