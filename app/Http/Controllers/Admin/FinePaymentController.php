<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinePayment;
use App\Models\Loan;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FinePaymentController extends Controller
{
    public static function methodSettings(): array
    {
        return [
            'cash_note'        => Setting::read('pay.cash_note', 'Bayar langsung ke petugas perpustakaan pada jam layanan.'),
            'ewallet_provider' => Setting::read('pay.ewallet_provider', ''),
            'ewallet_number'   => Setting::read('pay.ewallet_number', ''),
            'ewallet_holder'   => Setting::read('pay.ewallet_holder', ''),
            'qris_image'       => Setting::read('pay.qris_image', ''),
            'qris_note'        => Setting::read('pay.qris_note', ''),
        ];
    }

    public function index()
    {
        $base = Loan::where('status', 'Returned')->where('fine_amount', '>', 0);
        $stats = [
            'total'   => (int) (clone $base)->sum('fine_amount'),
            'paid'    => (int) (clone $base)->whereNotNull('fine_paid_at')->sum('fine_amount'),
            'unpaid'  => (int) (clone $base)->whereNull('fine_paid_at')->sum('fine_amount'),
            'pending' => FinePayment::where('status', 'Pending')->count(),
        ];

        $set = self::methodSettings();
        $pending = FinePayment::with(['loan.book', 'user'])->where('status', 'Pending')->latest('id')->get();
        $unpaid = Loan::with(['user', 'book'])->where('status', 'Returned')->where('fine_amount', '>', 0)->whereNull('fine_paid_at')->latest('returned_at')->get();
        $pendingLoanIds = $pending->pluck('loan_id')->all();
        $history = FinePayment::with(['loan.book', 'user', 'verifier'])->whereIn('status', ['Verified', 'Rejected'])->latest('id')->take(15)->get();
        $running = Loan::with(['user', 'book'])->where('status', 'Borrowed')->whereDate('due_date', '<', today())->get();

        return view('admin.fines.index', compact('stats', 'set', 'pending', 'unpaid', 'pendingLoanIds', 'history', 'running'));
    }

    public function settings(Request $request)
    {
        $data = $request->validate([
            'cash_note'        => 'nullable|string|max:255',
            'ewallet_provider' => 'nullable|in:DANA,OVO,GoPay,ShopeePay,LinkAja',
            'ewallet_number'   => 'nullable|regex:/^[0-9]{8,16}$/',
            'ewallet_holder'   => 'nullable|string|max:100',
            'qris_note'        => 'nullable|string|max:255',
            'qris'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ], ['ewallet_number.regex' => 'Nomor e-wallet harus 8 sampai 16 digit angka.']);

        foreach (['cash_note', 'ewallet_provider', 'ewallet_number', 'ewallet_holder', 'qris_note'] as $k) {
            Setting::put('pay.' . $k, $data[$k] ?? '');
        }

        if ($request->hasFile('qris')) {
            $old = Setting::read('pay.qris_image');
            if ($old) { Storage::disk('public')->delete($old); }
            Setting::put('pay.qris_image', $request->file('qris')->store('payment', 'public'));
        }

        return back()->with('success', 'Metode pembayaran disimpan.');
    }

    public function verify(Request $request, FinePayment $payment)
    {
        $adminId = $request->user()->id;
        $error = DB::transaction(function () use ($payment, $adminId) {
            $p = FinePayment::lockForUpdate()->find($payment->id);
            if (!$p || $p->status !== 'Pending') { return 'Pembayaran ini sudah diproses.'; }
            $loan = Loan::lockForUpdate()->find($p->loan_id);
            if (!$loan || $loan->fine_paid_at) { return 'Denda ini sudah lunas.'; }
            $p->update(['status' => 'Verified', 'verified_by' => $adminId, 'verified_at' => now()]);
            $loan->update(['fine_paid_at' => now()]);
            FinePayment::where('loan_id', $loan->id)->where('status', 'Pending')->where('id', '!=', $p->id)
                ->update(['status' => 'Rejected', 'reject_reason' => 'Denda sudah lunas lewat pembayaran lain.']);
            return null;
        });
        return $error ? back()->with('error', $error) : back()->with('success', 'Pembayaran diverifikasi, denda lunas.');
    }

    public function reject(Request $request, FinePayment $payment)
    {
        $data = $request->validate(['reason' => 'required|string|max:255']);
        if ($payment->status !== 'Pending') { return back()->with('error', 'Pembayaran ini sudah diproses.'); }
        $payment->update(['status' => 'Rejected', 'reject_reason' => $data['reason'], 'verified_by' => $request->user()->id, 'verified_at' => now()]);
        return back()->with('success', 'Pembayaran ditolak. Siswa bisa mengirim ulang.');
    }

    public function cash(Request $request, Loan $loan)
    {
        $adminId = $request->user()->id;
        $error = DB::transaction(function () use ($loan, $adminId) {
            $l = Loan::lockForUpdate()->find($loan->id);
            if (!$l || $l->status !== 'Returned' || $l->fine_amount <= 0 || $l->fine_paid_at) { return 'Tidak ada denda yang perlu dibayar.'; }
            FinePayment::where('loan_id', $l->id)->where('status', 'Pending')->update(['status' => 'Rejected', 'reject_reason' => 'Dibayar tunai di perpustakaan.']);
            FinePayment::create([
                'loan_id' => $l->id, 'user_id' => $l->user_id, 'amount' => $l->fine_amount, 'method' => 'cash',
                'status' => 'Verified', 'verified_by' => $adminId, 'verified_at' => now(), 'note' => 'Dicatat petugas',
            ]);
            $l->update(['fine_paid_at' => now()]);
            return null;
        });
        return $error ? back()->with('error', $error) : back()->with('success', 'Pembayaran tunai dicatat, denda lunas.');
    }
}