<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = ['user_id', 'book_id', 'book_copy_id', 'status', 'ready_at', 'expires_at'];

    protected function casts(): array
    {
        return ['ready_at' => 'datetime', 'expires_at' => 'datetime'];
    }

    public const LABELS = [
        'Waiting'   => 'Menunggu',
        'Ready'     => 'Siap Diambil',
        'Claimed'   => 'Diajukan',
        'Fulfilled' => 'Selesai',
        'Expired'   => 'Kedaluwarsa',
        'Cancelled' => 'Dibatalkan',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function book() { return $this->belongsTo(Book::class); }
    public function copy() { return $this->belongsTo(BookCopy::class, 'book_copy_id'); }

    public function getLabelAttribute(): string
    {
        return self::LABELS[$this->status] ?? $this->status;
    }
}