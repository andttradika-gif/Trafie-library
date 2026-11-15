<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingProgress extends Model
{
    protected $table = 'reading_progress';
    protected $fillable = ['user_id', 'book_id', 'status', 'current_page', 'total_pages', 'started_at', 'last_activity_at', 'completion_requested_at'];

    protected function casts(): array
    {
        return ['started_at' => 'datetime', 'last_activity_at' => 'datetime', 'completion_requested_at' => 'datetime'];
    }

    public const LABELS = ['Want' => 'Want to Read', 'Reading' => 'Currently Reading', 'Paused' => 'Paused', 'Completed' => 'Completed'];

    public function book() { return $this->belongsTo(Book::class); }

    public function getPercentAttribute(): int
    {
        if ($this->status === 'Completed') { return 100; }
        if (!$this->total_pages) { return 0; }
        return (int) min(100, round($this->current_page / $this->total_pages * 100));
    }
}