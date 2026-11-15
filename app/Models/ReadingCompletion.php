<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingCompletion extends Model
{
    protected $fillable = ['user_id', 'book_id', 'loan_id', 'rating', 'reflection', 'completed_at', 'status', 'revoked_at'];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime', 'revoked_at' => 'datetime'];
    }

    public function user() { return $this->belongsTo(User::class); }
    public function book() { return $this->belongsTo(Book::class); }
    public function loan() { return $this->belongsTo(Loan::class); }
}