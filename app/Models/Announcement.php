<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = ['title', 'body', 'type', 'is_pinned', 'is_active', 'published_at', 'ends_on', 'created_by'];

    protected function casts(): array
    {
        return ['is_pinned' => 'boolean', 'is_active' => 'boolean', 'published_at' => 'datetime', 'ends_on' => 'date'];
    }

    public const TYPES = ['info' => 'Informasi', 'penting' => 'Penting', 'event' => 'Acara'];

    public function scopeLive($q)
    {
        return $q->where('is_active', true)->where(function ($w) {
            $w->whereNull('ends_on')->orWhereDate('ends_on', '>=', today());
        });
    }
}