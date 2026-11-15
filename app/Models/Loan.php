<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $fillable = [
        'user_id', 'book_id', 'book_copy_id', 'duration_days', 'status',
        'requested_at', 'approved_at', 'approved_by', 'due_date', 'returned_at',
        'fine_amount', 'fine_paid_at',
    ];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'approved_at'  => 'datetime',
            'returned_at'  => 'datetime',
            'fine_paid_at' => 'datetime',
            'due_date'     => 'date',
        ];
    }

    public const LABELS = [
        'Pending'   => 'Menunggu',
        'Borrowed'  => 'Dipinjam',
        'Returned'  => 'Dikembalikan',
        'Rejected'  => 'Ditolak',
        'Cancelled' => 'Dibatalkan',
    ];

    public static function rupiah($n): string
    {
        return 'Rp ' . number_format((float) $n, 0, ',', '.');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function copy()
    {
        return $this->belongsTo(BookCopy::class, 'book_copy_id');
    }

    public function renewals()
    {
        return $this->hasMany(LoanRenewal::class);
    }

    public function getLabelAttribute(): string
    {
        return self::LABELS[$this->status] ?? $this->status;
    }

    public function isOverdue(): bool
    {
        return $this->status === 'Borrowed' && $this->due_date && $this->due_date->copy()->endOfDay()->isPast();
    }

    // Jumlah hari keterlambatan (berjalan kalau masih dipinjam, tetap kalau sudah dikembalikan)
    public function getLateDaysAttribute(): int
    {
        if (!in_array($this->status, ['Borrowed', 'Returned']) || !$this->due_date) {
            return 0;
        }
        $end = $this->returned_at ?? now();
        $due = $this->due_date->copy()->endOfDay();
        if (!$end->greaterThan($due)) {
            return 0;
        }
        return (int) ceil($due->diffInHours($end) / 24);
    }

    // Denda: berjalan kalau masih dipinjam, tetap (tersimpan) kalau sudah dikembalikan
    public function getCurrentFineAttribute(): int
    {
        if ($this->status === 'Borrowed') {
            return $this->late_days * (int) config('library.fine_per_day');
        }
        if ($this->status === 'Returned') {
            return (int) $this->fine_amount;
        }
        return 0;
    }

    // Status perpanjangan: none, pending, done, overdue, can
    public function renewalState(): string
    {
        if ($this->status !== 'Borrowed') {
            return 'none';
        }
        $r = $this->renewals;
        if ($r->where('status', 'Pending')->isNotEmpty()) {
            return 'pending';
        }
        if ($r->where('status', 'Approved')->isNotEmpty()) {
            return 'done';
        }
        if ($this->isOverdue()) {
            return 'overdue';
        }
        return 'can';
    }
}