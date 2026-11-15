<?php

use App\Http\Controllers\Admin\AdminExtraController as X;
use Illuminate\Support\Facades\Route;

// Dimuat dari DALAM grup admin di routes/web.php (prefix, nama, dan middleware ikut grup itu).
Route::get('eksemplar', [X::class, 'copies'])->name('extra.copies');
Route::get('denda', [X::class, 'fines'])->name('extra.fines');
Route::post('denda/{loan}/lunas', [X::class, 'payFine'])->name('extra.fines.pay');
Route::get('pembayaran', [X::class, 'payments'])->name('extra.payments');
Route::get('pengembalian', [X::class, 'returns'])->name('extra.returns');
Route::get('kalender', [X::class, 'calendar'])->name('extra.calendar');
Route::get('log-aktivitas', [X::class, 'activity'])->name('extra.activity');