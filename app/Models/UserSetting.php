<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $fillable = ['user_id', 'default_duration', 'n_due', 'n_reservation', 'n_fines', 'n_announcements'];

    protected function casts(): array
    {
        return ['n_due' => 'boolean', 'n_reservation' => 'boolean', 'n_fines' => 'boolean', 'n_announcements' => 'boolean'];
    }

    public static function forUser(int $userId): self
    {
        return static::firstOrNew(['user_id' => $userId], ['default_duration' => 7, 'n_due' => true, 'n_reservation' => true, 'n_fines' => true, 'n_announcements' => true]);
    }

    public static function wants(int $userId, string $cat): bool
    {
        $map = ['due' => 'n_due', 'reservation' => 'n_reservation', 'fines' => 'n_fines', 'announcements' => 'n_announcements'];
        if (!isset($map[$cat])) { return true; }
        $row = static::where('user_id', $userId)->first();
        return $row ? (bool) $row->{$map[$cat]} : true;
    }
}