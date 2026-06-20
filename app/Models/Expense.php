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
            'label'      => 'Belanja',
            'bg'         => 'bg-purple-100',
            'text'       => 'text-purple-600',
            'badge_bg'   => 'bg-purple-50',
            'badge_text' => 'text-purple-700',
            'badge_border' => 'border-purple-200',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>',
        ],
        'snacks' => [
            'label'      => 'Jajan',
            'bg'         => 'bg-orange-100',
            'text'       => 'text-orange-600',
            'badge_bg'   => 'bg-orange-50',
            'badge_text' => 'text-orange-700',
            'badge_border' => 'border-orange-200',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21l-9-4.5L3 21V8a2 2 0 012-2h14a2 2 0 012 2v13z"/></svg>',
        ],
        'personal needs' => [
            'label'      => 'Kebutuhan Pribadi',
            'bg'         => 'bg-pink-100',
            'text'       => 'text-pink-600',
            'badge_bg'   => 'bg-pink-50',
            'badge_text' => 'text-pink-700',
            'badge_border' => 'border-pink-200',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>',
        ],
        'transportation' => [
            'label'      => 'Transportasi',
            'bg'         => 'bg-sky-100',
            'text'       => 'text-sky-600',
            'badge_bg'   => 'bg-sky-50',
            'badge_text' => 'text-sky-700',
            'badge_border' => 'border-sky-200',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>',
        ],
        'savings' => [
            'label'      => 'Tabungan',
            'bg'         => 'bg-emerald-100',
            'text'       => 'text-emerald-600',
            'badge_bg'   => 'bg-emerald-50',
            'badge_text' => 'text-emerald-700',
            'badge_border' => 'border-emerald-200',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 2.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125m16.5 5.625c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/></svg>',
        ],
        'bills' => [
            'label'      => 'Tagihan',
            'bg'         => 'bg-yellow-100',
            'text'       => 'text-yellow-600',
            'badge_bg'   => 'bg-yellow-50',
            'badge_text' => 'text-yellow-700',
            'badge_border' => 'border-yellow-200',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>',
        ],
        'other' => [
            'label'      => 'Lainnya',
            'bg'         => 'bg-slate-100',
            'text'       => 'text-slate-600',
            'badge_bg'   => 'bg-slate-50',
            'badge_text' => 'text-slate-700',
            'badge_border' => 'border-slate-200',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
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
