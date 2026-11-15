<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class LibrarySettingsProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        try {
            if (!Schema::hasTable('settings')) { return; }
            $rows = Setting::whereIn('key', ['lib.fine_per_day', 'lib.max_loans', 'lib.reserve_hold_days'])->pluck('value', 'key');
            foreach (['fine_per_day', 'max_loans', 'reserve_hold_days'] as $k) {
                $v = $rows['lib.' . $k] ?? null;
                if ($v !== null && $v !== '') { config(['library.' . $k => (int) $v]); }
            }
        } catch (\Throwable $e) {
            // abaikan: pakai nilai bawaan config
        }
    }
}