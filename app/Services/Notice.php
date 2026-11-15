<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSetting;
use App\Notifications\TrafieNotice;

class Notice
{
    // $cat: loans (selalu dikirim), due, reservation, fines, announcements
    public static function to(User $u, string $cat, string $title, string $body, ?string $url = null, string $icon = 'bell', ?string $key = null): void
    {
        try {
            if (!UserSetting::wants($u->id, $cat)) { return; }
            $u->notify(new TrafieNotice($title, $body, $url, $icon, $key));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}