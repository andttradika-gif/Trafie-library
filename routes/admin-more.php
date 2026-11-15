<?php

use App\Http\Controllers\Admin;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/pengaturan', [Admin\SettingController::class, 'edit'])->name('settings.edit');
    Route::put('/pengaturan', [Admin\SettingController::class, 'update'])->name('settings.update');
});