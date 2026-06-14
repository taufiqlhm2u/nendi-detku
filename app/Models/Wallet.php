<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance'];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    // ──────────────────────────────────────────────
    // Relasi
    // ──────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ──────────────────────────────────────────────
    // Helper: tambah saldo (income)
    // ──────────────────────────────────────────────
    public function credit(float $amount): void
    {
        $this->increment('balance', $amount);
    }

    // ──────────────────────────────────────────────
    // Helper: kurangi saldo (expense)
    // ──────────────────────────────────────────────
    public function debit(float $amount): void
    {
        $this->decrement('balance', $amount);
    }
}
