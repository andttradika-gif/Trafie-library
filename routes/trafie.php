<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AvatarController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/reservasi', [Admin\ReservationController::class, 'index'])->name('reservations.index');
    Route::patch('/reservasi/{reservation}/siap', [Admin\ReservationController::class, 'ready'])->name('reservations.ready');
    Route::patch('/reservasi/{reservation}/batal', [Admin\ReservationController::class, 'cancel'])->name('reservations.cancel');

    Route::get('/pembayaran-denda', [Admin\FinePaymentController::class, 'index'])->name('fines.index');
    Route::put('/pembayaran-denda/metode', [Admin\FinePaymentController::class, 'settings'])->name('fines.settings');
    Route::patch('/pembayaran-denda/{payment}/verifikasi', [Admin\FinePaymentController::class, 'verify'])->name('fines.verify');
    Route::patch('/pembayaran-denda/{payment}/tolak', [Admin\FinePaymentController::class, 'reject'])->name('fines.reject');
    Route::post('/pembayaran-denda/{loan}/tunai', [Admin\FinePaymentController::class, 'cash'])->name('fines.cash');

    Route::get('/ulasan-rating', [Admin\ReviewBoardController::class, 'index'])->name('reviewboard.index');
    Route::patch('/ulasan-rating/{review}/sembunyikan', [Admin\ReviewBoardController::class, 'hide'])->name('reviewboard.hide');

    Route::get('/tantangan', [Admin\ChallengeController::class, 'index'])->name('challenges.index');
    Route::post('/tantangan', [Admin\ChallengeController::class, 'store'])->name('challenges.store');
    Route::patch('/tantangan/{challenge}/status', [Admin\ChallengeController::class, 'toggle'])->name('challenges.toggle');
    Route::delete('/tantangan/{challenge}', [Admin\ChallengeController::class, 'destroy'])->name('challenges.destroy');

    Route::get('/pengumuman', [Admin\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/pengumuman', [Admin\AnnouncementController::class, 'store'])->name('announcements.store');
    Route::patch('/pengumuman/{announcement}/tampil', [Admin\AnnouncementController::class, 'toggle'])->name('announcements.toggle');
    Route::delete('/pengumuman/{announcement}', [Admin\AnnouncementController::class, 'destroy'])->name('announcements.destroy');
});

Route::middleware('auth')->group(function () {
    Route::post('/akun/foto', [AvatarController::class, 'update'])->name('account.avatar');
    Route::delete('/akun/foto', [AvatarController::class, 'destroy'])->name('account.avatar.delete');
});