<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingChallenge extends Model
{
    protected $fillable = ['title', 'description', 'starts_on', 'ends_on', 'target_books', 'category_id', 'reward_xp', 'badge_name', 'status', 'created_by'];

    protected function casts(): array
    {
        return ['starts_on' => 'date', 'ends_on' => 'date'];
    }

    public const LABELS = ['Active' => 'Aktif', 'Closed' => 'Ditutup'];

    public function category() { return $this->belongsTo(Category::class); }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'challenge_participants', 'challenge_id', 'user_id')->withPivot(['joined_at', 'completed_at']);
    }
}