<?php

use App\Http\Controllers\Student;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/categories', [Student\CategoryController::class, 'index'])->name('categories.index');

    Route::get('/reservations', [Student\ReservationController::class, 'index'])->name('reservations.index');
    Route::post('/books/{book}/reservasi', [Student\ReservationController::class, 'store'])->name('reservations.store');
    Route::patch('/reservations/{reservation}/batal', [Student\ReservationController::class, 'cancel'])->name('reservations.cancel');
    Route::post('/reservations/{reservation}/ambil', [Student\ReservationController::class, 'claim'])->name('reservations.claim');

    Route::get('/favorites', [Student\FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{book}', [Student\FavoriteController::class, 'toggle'])->name('favorites.toggle');

    Route::get('/denda', [Student\FineController::class, 'index'])->name('fines.index');
    Route::get('/denda/{loan}/bayar', [Student\FineController::class, 'pay'])->name('fines.pay');
    Route::post('/denda/{loan}/bayar', [Student\FineController::class, 'store'])->name('fines.store');

    Route::get('/notifications', [Student\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/buka', [Student\NotificationController::class, 'open'])->name('notifications.open');
    Route::post('/notifications/baca-semua', [Student\NotificationController::class, 'readAll'])->name('notifications.readall');

    Route::get('/pengumuman', [Student\AnnouncementController::class, 'index'])->name('announcements.index');
    Route::get('/profil', [Student\OverviewController::class, 'show'])->name('profile.show');
    Route::get('/pengaturan', [Student\SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/pengaturan', [Student\SettingController::class, 'update'])->name('settings.update');
});