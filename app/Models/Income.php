<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    protected $fillable = ['user_id', 'type', 'amount', 'image', 'date', 'note'];

    protected $casts = [
        'date' => 'datetime'
    ];
    /**
     * Konfigurasi warna & icon SVG per tipe pemasukan.
     * Key 'other' digunakan juga sebagai fallback.
     */
    public static array $typeConfig = [
        'salary' => [
            'label'      => 'Gaji',
            'bg'         => 'bg-blue-100',
            'text'       => 'text-blue-600',
            'badge_bg'   => 'bg-blue-50',
            'badge_text' => 'text-blue-700',
            'badge_border' => 'border-blue-200',
            'icon'       => '<span class="material-symbols-outlined text-[24px]">payments</span>',
        ],
        'allowance' => [
            'label'      => 'Tunjangan',
            'bg'         => 'bg-teal-100',
            'text'       => 'text-teal-600',
            'badge_bg'   => 'bg-teal-50',
            'badge_text' => 'text-teal-700',
            'badge_border' => 'border-teal-200',
            'icon'       => '<span class="material-symbols-outlined text-[24px]">card_giftcard</span>',
        ],
        'freelance' => [
            'label'      => 'Freelance',
            'bg'         => 'bg-violet-100',
            'text'       => 'text-violet-600',
            'badge_bg'   => 'bg-violet-50',
            'badge_text' => 'text-violet-700',
            'badge_border' => 'border-violet-200',
            'icon'       => '<span class="material-symbols-outlined text-[24px]">work</span>',
        ],
        'bonus' => [
            'label'      => 'Bonus',
            'bg'         => 'bg-amber-100',
            'text'       => 'text-amber-600',
            'badge_bg'   => 'bg-amber-50',
            'badge_text' => 'text-amber-700',
            'badge_border' => 'border-amber-200',
            'icon'       => '<span class="material-symbols-outlined text-[24px]">workspace_premium</span>',
        ],
        'investment' => [
            'label'      => 'Investasi',
            'bg'         => 'bg-emerald-100',
            'text'       => 'text-emerald-600',
            'badge_bg'   => 'bg-emerald-50',
            'badge_text' => 'text-emerald-700',
            'badge_border' => 'border-emerald-200',
            'icon'       => '<span class="material-symbols-outlined text-[24px]">trending_up</span>',
        ],
        'other' => [
            'label'      => 'Lainnya',
            'bg'         => 'bg-slate-100',
            'text'       => 'text-slate-600',
            'badge_bg'   => 'bg-slate-50',
            'badge_text' => 'text-slate-700',
            'badge_border' => 'border-slate-200',
            'icon'       => '<span class="material-symbols-outlined text-[24px]">more_horiz</span>',
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
