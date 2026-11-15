<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanRenewal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RenewalController extends Controller
{
    public function store(Request $request, Loan $loan)
    {
        abort_if($loan->user_id !== $request->user()->id, 403);
        $data = $request->validate(['extra_days' => 'required|in:3,7']);

        $error = DB::transaction(function () use ($loan, $data) {
            $loan = Loan::lockForUpdate()->find($loan->id);
            if (!$loan || $loan->status !== 'Borrowed') {
                return 'Hanya buku yang sedang dipinjam yang bisa diperpanjang.';
            }

            $loan->load('renewals');
            switch ($loan->renewalState()) {
                case 'pending':
                    return 'Permintaan perpanjangan untuk buku ini masih menunggu admin.';
                case 'done':
                    return 'Buku ini sudah pernah diperpanjang (maksimal 1 kali).';
                case 'overdue':
                    return 'Pinjaman sudah lewat jatuh tempo, kembalikan bukunya dulu.';
            }

            LoanRenewal::create([
                'loan_id'    => $loan->id,
                'extra_days' => (int) $data['extra_days'],
                'status'     => 'Pending',
            ]);
            return null;
        });

        return $error
            ? back()->with('error', $error)
            : back()->with('success', 'Permintaan perpanjangan ' . $data['extra_days'] . ' hari dikirim. Tunggu persetujuan admin.');
    }
}