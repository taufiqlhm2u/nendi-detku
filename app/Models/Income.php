<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Income extends Model
{
    protected $fillable = ['user_id', 'type', 'amount', 'image', 'note'];

    /**
     * Konfigurasi warna & icon SVG per tipe pemasukan.
     * Key 'other' digunakan juga sebagai fallback.
     */
    public static array $typeConfig = [
        'Salary' => [
            'label'      => 'Gaji',
            'bg'         => 'bg-blue-100',
            'text'       => 'text-blue-600',
            'badge_bg'   => 'bg-blue-50',
            'badge_text' => 'text-blue-700',
            'badge_border'=> 'border-blue-200',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>',
        ],
        'allowance' => [
            'label'      => 'Tunjangan',
            'bg'         => 'bg-teal-100',
            'text'       => 'text-teal-600',
            'badge_bg'   => 'bg-teal-50',
            'badge_text' => 'text-teal-700',
            'badge_border'=> 'border-teal-200',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
        ],
        'Freelance' => [
            'label'      => 'Freelance',
            'bg'         => 'bg-violet-100',
            'text'       => 'text-violet-600',
            'badge_bg'   => 'bg-violet-50',
            'badge_text' => 'text-violet-700',
            'badge_border'=> 'border-violet-200',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z"/></svg>',
        ],
        'Bonus' => [
            'label'      => 'Bonus',
            'bg'         => 'bg-amber-100',
            'text'       => 'text-amber-600',
            'badge_bg'   => 'bg-amber-50',
            'badge_text' => 'text-amber-700',
            'badge_border'=> 'border-amber-200',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>',
        ],
        'Investment' => [
            'label'      => 'Investasi',
            'bg'         => 'bg-emerald-100',
            'text'       => 'text-emerald-600',
            'badge_bg'   => 'bg-emerald-50',
            'badge_text' => 'text-emerald-700',
            'badge_border'=> 'border-emerald-200',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941"/></svg>',
        ],
        'Other' => [
            'label'      => 'Lainnya',
            'bg'         => 'bg-slate-100',
            'text'       => 'text-slate-600',
            'badge_bg'   => 'bg-slate-50',
            'badge_text' => 'text-slate-700',
            'badge_border'=> 'border-slate-200',
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
