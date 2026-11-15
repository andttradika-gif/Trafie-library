<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoanRenewal extends Model
{
    protected $fillable = ['loan_id', 'extra_days', 'status', 'decided_by', 'decided_at'];

    protected function casts(): array
    {
        return ['decided_at' => 'datetime'];
    }

    public const LABELS = [
        'Pending'  => 'Menunggu',
        'Approved' => 'Disetujui',
        'Rejected' => 'Ditolak',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function getLabelAttribute(): string
    {
        return self::LABELS[$this->status] ?? $this->status;
    }
}