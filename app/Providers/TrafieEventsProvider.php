<?php

namespace App\Providers;

use App\Models\Announcement;
use App\Models\BookCopy;
use App\Models\FinePayment;
use App\Models\Loan;
use App\Models\Reservation;
use App\Models\User;
use App\Services\Notice;
use App\Services\Reservations;
use Illuminate\Support\ServiceProvider;

class TrafieEventsProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Loan::updated(function (Loan $loan) {
            try {
                $u = $loan->user;
                if (!$u) { return; }
                $judul = $loan->book->title ?? 'buku';

                if ($loan->wasChanged('status')) {
                    if ($loan->status === 'Borrowed') {
                        Notice::to($u, 'loans', 'Peminjaman disetujui', '"' . $judul . '" siap diambil. Jatuh tempo ' . ($loan->due_date ? $loan->due_date->format('d M Y') : '-') . '.', route('student.loans.index', [], false), 'bookmark');
                    } elseif ($loan->status === 'Rejected') {
                        Notice::to($u, 'loans', 'Peminjaman ditolak', 'Permintaan pinjam "' . $judul . '" tidak disetujui.', route('student.loans.index', [], false), 'bookmark');
                    } elseif ($loan->status === 'Returned' && (int) $loan->fine_amount > 0) {
                        Notice::to($u, 'fines', 'Denda dibuat', 'Denda keterlambatan "' . $judul . '" sebesar ' . Loan::rupiah($loan->fine_amount) . '. Bayar lewat menu Pembayaran Denda.', route('student.fines.index', [], false), 'coin');
                    }

                    // Pinjam dari reservasi ditolak atau dibatalkan: lepas eksemplar, panggil antrean berikutnya
                    if (in_array($loan->status, ['Rejected', 'Cancelled']) && $loan->book_copy_id) {
                        $res = Reservation::where('book_copy_id', $loan->book_copy_id)->where('status', 'Claimed')->first();
                        if ($res) {
                            $res->update(['status' => 'Cancelled']);
                            BookCopy::where('id', $loan->book_copy_id)->where('status', 'Reserved')->update(['status' => 'Available']);
                            Reservations::promote((int) $loan->book_id);
                        }
                    }
                }

                if ($loan->wasChanged('fine_paid_at') && $loan->fine_paid_at) {
                    Notice::to($u, 'fines', 'Denda lunas', 'Denda ' . Loan::rupiah($loan->fine_amount) . ' untuk "' . $judul . '" sudah lunas.', route('student.fines.index', [], false), 'coin');
                }
            } catch (\Throwable $e) { report($e); }
        });

        FinePayment::updated(function (FinePayment $p) {
            try {
                if ($p->wasChanged('status') && $p->status === 'Rejected' && $p->user) {
                    Notice::to($p->user, 'fines', 'Pembayaran ditolak', ($p->reject_reason ?: 'Bukti pembayaran tidak valid.') . ' Silakan kirim ulang.', route('student.fines.index', [], false), 'coin');
                }
            } catch (\Throwable $e) { report($e); }
        });

        Reservation::updated(function (Reservation $r) {
            try {
                if (!$r->wasChanged('status') || !$r->user) { return; }
                $judul = $r->book->title ?? 'buku';
                if ($r->status === 'Ready') {
                    Notice::to($r->user, 'reservation', 'Reservasi tersedia', '"' . $judul . '" siap untukmu. Ajukan pinjam sebelum ' . ($r->expires_at ? $r->expires_at->format('d M Y H:i') : '-') . '.', route('student.reservations.index', [], false), 'clock');
                } elseif ($r->status === 'Expired') {
                    Notice::to($r->user, 'reservation', 'Reservasi kedaluwarsa', 'Batas ambil "' . $judul . '" sudah lewat.', route('student.reservations.index', [], false), 'clock');
                }
            } catch (\Throwable $e) { report($e); }
        });

        Announcement::created(function (Announcement $a) {
            try {
                if (!$a->is_active) { return; }
                User::where('role', 'student')->chunk(200, function ($users) use ($a) {
                    foreach ($users as $u) {
                        Notice::to($u, 'announcements', 'Pengumuman baru', $a->title, route('student.announcements.index', [], false), 'mega');
                    }
                });
            } catch (\Throwable $e) { report($e); }
        });
    }
}