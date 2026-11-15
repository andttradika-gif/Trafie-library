<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinePayment extends Model
{
    protected $fillable = ['loan_id', 'user_id', 'amount', 'method', 'status', 'proof_path', 'reference', 'note', 'reject_reason', 'verified_by', 'verified_at'];

    protected function casts(): array
    {
        return ['verified_at' => 'datetime'];
    }

    public const METHODS = ['cash' => 'Tunai', 'ewallet' => 'E-Wallet', 'qris' => 'QRIS'];
    public const LABELS = ['Pending' => 'Menunggu Verifikasi', 'Verified' => 'Terverifikasi', 'Rejected' => 'Ditolak'];

    public function loan() { return $this->belongsTo(Loan::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function verifier() { return $this->belongsTo(User::class, 'verified_by'); }

    public function getMethodLabelAttribute(): string
    {
        return self::METHODS[$this->method] ?? $this->method;
    }

    public function getLabelAttribute(): string
    {
        return self::LABELS[$this->status] ?? $this->status;
    }

    public function getProofUrlAttribute(): ?string
    {
        return $this->proof_path ? asset('storage/' . $this->proof_path) : null;
    }
}