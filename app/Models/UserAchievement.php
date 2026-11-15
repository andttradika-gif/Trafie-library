<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAchievement extends Model
{
    protected $fillable = ['user_id', 'code', 'unlocked_at'];

    protected function casts(): array
    {
        return ['unlocked_at' => 'datetime'];
    }
}