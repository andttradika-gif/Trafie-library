<?php

namespace App\Services;

use App\Models\BookCopy;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class Reservations
{
    public static function holdDays(): int
    {
        return (int) config('library.reserve_hold_days', 2);
    }

    // Beri eksemplar yang tersedia ke antrean tertua untuk buku ini
    public static function promote(int $bookId): void
    {
        DB::transaction(function () use ($bookId) {
            while (true) {
                $res = Reservation::where('book_id', $bookId)->where('status', 'Waiting')->orderBy('id')->lockForUpdate()->first();
                if (!$res) { break; }
                $copy = BookCopy::where('book_id', $bookId)->where('status', 'Available')->lockForUpdate()->first();
                if (!$copy) { break; }
                $copy->update(['status' => 'Reserved']);
                $res->update([
                    'status'       => 'Ready',
                    'book_copy_id' => $copy->id,
                    'ready_at'     => now(),
                    'expires_at'   => now()->addDays(self::holdDays()),
                ]);
            }
        });
    }

    // Reservasi siap ambil yang lewat batas waktu: kedaluwarsa, eksemplar dilepas, antrean berikutnya dipanggil
    public static function expireDue(): int
    {
        $due = Reservation::where('status', 'Ready')->where('expires_at', '<', now())->get();
        $books = [];
        foreach ($due as $r) {
            DB::transaction(function () use ($r, &$books) {
                $row = Reservation::lockForUpdate()->find($r->id);
                if (!$row || $row->status !== 'Ready') { return; }
                if ($row->book_copy_id) {
                    BookCopy::where('id', $row->book_copy_id)->where('status', 'Reserved')->update(['status' => 'Available']);
                }
                $row->update(['status' => 'Expired']);
                $books[$row->book_id] = true;
            });
        }
        foreach (array_keys($books) as $b) { self::promote((int) $b); }
        return count($due);
    }

    // Batalkan antrean. Mengembalikan pesan error atau null kalau berhasil.
    public static function cancel(Reservation $r): ?string
    {
        $bookId = null;
        $error = DB::transaction(function () use ($r, &$bookId) {
            $row = Reservation::lockForUpdate()->find($r->id);
            if (!$row) { return 'Reservasi tidak ditemukan.'; }
            if (!in_array($row->status, ['Waiting', 'Ready'])) { return 'Reservasi ini tidak bisa dibatalkan lagi.'; }
            if ($row->status === 'Ready' && $row->book_copy_id) {
                BookCopy::where('id', $row->book_copy_id)->where('status', 'Reserved')->update(['status' => 'Available']);
            }
            $row->update(['status' => 'Cancelled']);
            $bookId = $row->book_id;
            return null;
        });
        if (!$error && $bookId) { self::promote((int) $bookId); }
        return $error;
    }
}