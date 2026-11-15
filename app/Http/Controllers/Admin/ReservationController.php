<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Services\Reservations;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        Reservations::expireDue();

        $status = (string) $request->query('status', '');
        if (!array_key_exists($status, Reservation::LABELS)) { $status = ''; }

        $items = Reservation::with(['user', 'book', 'copy'])
            ->when($status !== '', fn ($q) => $q->where('status', $status))
            ->orderByRaw("FIELD(status, 'Ready', 'Waiting', 'Claimed', 'Fulfilled', 'Expired', 'Cancelled')")
            ->orderBy('id')
            ->paginate(15)->withQueryString();

        $counts = Reservation::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $count = [];
        $posById = [];
        foreach (Reservation::where('status', 'Waiting')->orderBy('id')->get(['id', 'book_id']) as $w) {
            $count[$w->book_id] = ($count[$w->book_id] ?? 0) + 1;
            $posById[$w->id] = $count[$w->book_id];
        }

        return view('admin.reservations.index', compact('items', 'status', 'counts', 'posById'));
    }

    public function ready(Reservation $reservation)
    {
        if ($reservation->status !== 'Waiting') {
            return back()->with('error', 'Reservasi ini tidak sedang menunggu.');
        }
        Reservations::promote((int) $reservation->book_id);
        $fresh = $reservation->fresh();
        return $fresh->status === 'Ready'
            ? back()->with('success', 'Eksemplar disiapkan untuk antrean pertama buku ini.')
            : back()->with('error', 'Belum ada eksemplar tersedia untuk buku ini.');
    }

    public function cancel(Reservation $reservation)
    {
        $error = Reservations::cancel($reservation);
        return $error ? back()->with('error', $error) : back()->with('success', 'Reservasi dibatalkan.');
    }
}