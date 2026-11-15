<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Admin\FinePaymentController;
use App\Http\Controllers\Controller;
use App\Models\FinePayment;
use App\Models\Loan;
use Illuminate\Http\Request;

class FineController extends Controller
{
    private function own(Request $request, Loan $loan): void
    {
        abort_unless($loan->user_id === $request->user()->id, 403);
    }

    public function index(Request $request)
    {
        $uid = $request->user()->id;
        $fined = Loan::with('book')->where('user_id', $uid)->where('status', 'Returned')->where('fine_amount', '>', 0)->latest('returned_at')->get();
        $unpaid = $fined->filter(fn ($l) => !$l->fine_paid_at)->values();
        $paid = $fined->filter(fn ($l) => (bool) $l->fine_paid_at)->values();
        $running = Loan::with('book')->where('user_id', $uid)->where('status', 'Borrowed')->whereDate('due_date', '<', today())->get();
        $payments = FinePayment::with('loan.book')->where('user_id', $uid)->latest('id')->take(10)->get();
        $pendingIds = FinePayment::where('user_id', $uid)->where('status', 'Pending')->pluck('loan_id')->all();
        $set = FinePaymentController::methodSettings();

        $totals = [
            'unpaid'  => (int) $unpaid->sum('fine_amount'),
            'paid'    => (int) $paid->sum('fine_amount'),
            'running' => (int) $running->sum(fn ($l) => $l->current_fine),
        ];

        return view('student.fines.index', compact('unpaid', 'paid', 'running', 'payments', 'pendingIds', 'set', 'totals'));
    }

    public function pay(Request $request, Loan $loan)
    {
        $this->own($request, $loan);
        if ($loan->status !== 'Returned' || $loan->fine_amount <= 0 || $loan->fine_paid_at) {
            return redirect()->route('student.fines.index')->with('error', 'Denda ini tidak perlu dibayar.');
        }
        $loan->load('book');
        $set = FinePaymentController::methodSettings();
        $pending = FinePayment::where('loan_id', $loan->id)->where('status', 'Pending')->latest('id')->first();
        $last = FinePayment::where('loan_id', $loan->id)->where('status', 'Rejected')->latest('id')->first();
        return view('student.fines.pay', compact('loan', 'set', 'pending', 'last'));
    }

    public function store(Request $request, Loan $loan)
    {
        $this->own($request, $loan);
        if ($loan->status !== 'Returned' || $loan->fine_amount <= 0 || $loan->fine_paid_at) {
            return redirect()->route('student.fines.index')->with('error', 'Denda ini tidak perlu dibayar.');
        }

        $data = $request->validate([
            'method'    => 'required|in:cash,ewallet,qris',
            'reference' => 'nullable|string|max:100',
            'proof'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ], ['proof.image' => 'Bukti harus berupa gambar.', 'proof.max' => 'Ukuran bukti maksimal 3 MB.']);

        if ($data['method'] === 'cash') {
            return back()->with('success', 'Bayar tunai langsung ke petugas perpustakaan. Petugas akan mencatat pembayaranmu dan denda otomatis lunas.');
        }
        if (!$request->hasFile('proof')) {
            return back()->withErrors(['proof' => 'Unggah bukti pembayaran (screenshot atau foto struk).'])->withInput();
        }

        $set = FinePaymentController::methodSettings();
        if ($data['method'] === 'ewallet' && ($set['ewallet_number'] === '' || $set['ewallet_provider'] === '')) {
            return back()->with('error', 'Metode E-Wallet belum diatur admin. Pilih metode lain atau bayar tunai.');
        }
        if ($data['method'] === 'qris' && $set['qris_image'] === '') {
            return back()->with('error', 'QRIS belum diunggah admin. Pilih metode lain atau bayar tunai.');
        }
        if (FinePayment::where('loan_id', $loan->id)->where('status', 'Pending')->exists()) {
            return back()->with('error', 'Bukti pembayaranmu sedang menunggu verifikasi admin.');
        }

        FinePayment::create([
            'loan_id' => $loan->id, 'user_id' => $request->user()->id, 'amount' => (int) $loan->fine_amount,
            'method' => $data['method'], 'status' => 'Pending', 'reference' => $data['reference'] ?? null,
            'proof_path' => $request->file('proof')->store('proofs', 'public'),
        ]);

        return redirect()->route('student.fines.index')->with('success', 'Bukti terkirim. Denda lunas setelah diverifikasi admin.');
    }
}