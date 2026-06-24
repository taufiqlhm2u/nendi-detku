<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = ['user_id', 'type', 'amount', 'image', 'date', 'note'];

    protected $casts = [
        'date' => 'datetime'
    ];
    /**
     * Konfigurasi warna & icon SVG per tipe pengeluaran.
     * Key 'Other' digunakan sebagai fallback.
     */
    public static array $typeConfig = [
    'shopping' => [
        'label'        => 'Belanja',
        'bg'           => 'bg-purple-100',
        'text'         => 'text-purple-600',
        'badge_bg'     => 'bg-purple-50',
        'badge_text'   => 'text-purple-700',
        'badge_border' => 'border-purple-200',
        'icon'         => '<span class="material-symbols-outlined text-[24px]">local_mall</span>',
    ],
    'snacks' => [
        'label'        => 'Jajan',
        'bg'           => 'bg-orange-100',
        'text'         => 'text-orange-600',
        'badge_bg'     => 'bg-orange-50',
        'badge_text'   => 'text-orange-700',
        'badge_border' => 'border-orange-200',
        'icon'         => '<span class="material-symbols-outlined text-[24px]">lunch_dining</span>',
    ],
    'personal needs' => [
        'label'        => 'Kebutuhan Pribadi',
        'bg'           => 'bg-pink-100',
        'text'         => 'text-pink-600',
        'badge_bg'     => 'bg-pink-50',
        'badge_text'   => 'text-pink-700',
        'badge_border' => 'border-pink-200',
        'icon'         => '<span class="material-symbols-outlined text-[24px]">self_care</span>',
    ],
    'transportation' => [
        'label'        => 'Transportasi',
        'bg'           => 'bg-sky-100',
        'text'         => 'text-sky-600',
        'badge_bg'     => 'bg-sky-50',
        'badge_text'   => 'text-sky-700',
        'badge_border' => 'border-sky-200',
        'icon'         => '<span class="material-symbols-outlined text-[24px]">commute</span>',
    ],
    'savings' => [
        'label'        => 'Tabungan',
        'bg'           => 'bg-emerald-100',
        'text'         => 'text-emerald-600',
        'badge_bg'     => 'bg-emerald-50',
        'badge_text'   => 'text-emerald-700',
        'badge_border' => 'border-emerald-200',
        'icon'         => '<span class="material-symbols-outlined text-[24px]">piggy_bank</span>',
    ],
    'bills' => [
        'label'        => 'Tagihan',
        'bg'           => 'bg-yellow-100',
        'text'         => 'text-yellow-600',
        'badge_bg'     => 'bg-yellow-50',
        'badge_text'   => 'text-yellow-700',
        'badge_border' => 'border-yellow-200',
        'icon'         => '<span class="material-symbols-outlined text-[24px]">description</span>',
    ],
    'other' => [
        'label'        => 'Lainnya',
        'bg'           => 'bg-slate-100',
        'text'         => 'text-slate-600',
        'badge_bg'     => 'bg-slate-50',
        'badge_text'   => 'text-slate-700',
        'badge_border' => 'border-slate-200',
        'icon'         => '<span class="material-symbols-outlined text-[24px]">category</span>',
    ],
];

    /**
     * Ambil konfigurasi warna & icon berdasarkan tipe.
     */
    public static function getTypeConfig(string $type): array
    {
        return static::$typeConfig[$type] ?? static::$typeConfig['Other'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
