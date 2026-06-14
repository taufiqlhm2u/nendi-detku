<?php

use Livewire\Component;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    public string $period = 'daily';

    /**
     * Live polling: refresh data setiap 30 detik otomatis.
     * Bisa juga pakai wire:poll di template — sudah ditambahkan di bawah.
     */

    public function with(): array
    {
        $user = Auth::user();

        [$startDate, $labels, $groupFormat] = match ($this->period) {
            'weekly' => [
                Carbon::now()->startOfMonth(),
                $this->buildWeeklyLabels(),
                'W-Y', // format: nomor-minggu-ISO-tahun
            ],
            'monthly' => [
                // Mulai dari bulan pertama user punya transaksi (income ATAU expense)
                $this->firstTransactionMonth($user->id),
                $this->buildMonthlyLabels($user->id),
                'Y-m',
            ],
            default => [
                // daily
                Carbon::now()->startOfWeek(Carbon::MONDAY),
                ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                'N', // N = nomor hari ISO (1=Sen … 7=Min)
            ],
        };

        $chartData = $this->buildChartData($user->id, $this->period, $startDate, $labels, $groupFormat);

        $wallet = Wallet::where('user_id', $user->id)->first();
        $totalIncome = Income::where('user_id', $user->id)->sum('amount');
        $totalExpense = Expense::where('user_id', $user->id)->sum('amount');
        $balance = $wallet ? (float) $wallet->balance : $totalIncome - $totalExpense;

        $incomes = Income::where('user_id', $user->id)->orderByDesc('created_at')->limit(10)->get()->map(
            fn($i) => [
                'type_group' => 'income',
                'type' => $i->type,
                'amount' => $i->amount,
                'note' => $i->note,
                'created_at' => $i->created_at,
                'config' => Income::getTypeConfig($i->type),
            ],
        );

        $expenses = Expense::where('user_id', $user->id)->orderByDesc('created_at')->limit(10)->get()->map(
            fn($e) => [
                'type_group' => 'expense',
                'type' => $e->type,
                'amount' => $e->amount,
                'note' => $e->note,
                'created_at' => $e->created_at,
                'config' => Expense::getTypeConfig($e->type),
            ],
        );

        $transactions = $incomes->concat($expenses)->sortByDesc('created_at')->take(10)->values();

        return compact('balance', 'totalIncome', 'totalExpense', 'transactions', 'chartData');
    }

    // ──────────────────────────────────────────────────────────
    // Helpers: chart
    // ──────────────────────────────────────────────────────────

    private function buildChartData(int $userId, string $period, Carbon $startDate, array $labels, string $groupFormat): array
    {
        $endDate = match ($period) {
            'monthly' => Carbon::now()->endOfMonth(),
            'weekly' => Carbon::now()->endOfMonth(),
            default => Carbon::now()->endOfWeek(Carbon::SUNDAY),
        };

        $rawExpenses = Expense::where('user_id', $userId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $grouped = $rawExpenses->groupBy(fn($e) => $e->created_at->format($groupFormat));

        if ($period === 'daily') {
            // Senin (1) … Minggu (7)
            $data = collect(range(1, 7))->map(fn($d) => (int) ($grouped->get((string) $d)?->sum('amount') ?? 0));
            $displayLabels = collect($labels);
        } else {
            // weekly & monthly — labels adalah array ['key'=>..., 'display'=>...]
            $keys = collect($labels)->pluck('key');
            $data = $keys->map(fn($k) => (int) ($grouped->get($k)?->sum('amount') ?? 0));
            $displayLabels = collect($labels)->pluck('display');
        }

        $max = $data->max() ?: 1;

        // Index bar yang sedang aktif (hari ini / minggu ini / bulan ini)
        $todayIndex = match ($period) {
            'daily' => (int) Carbon::now()->format('N') - 1, // N=1(Sen)…7(Min) → index 0…6
            'weekly' => (int) (collect($labels)
                ->pluck('key')
                ->search(Carbon::now()->format('W') . '-' . Carbon::now()->format('Y')) ?? 0),
            'monthly' => (int) (collect($labels)
                ->pluck('key')
                ->search(Carbon::now()->format('Y-m')) ?? 0),
        };

        return [
            'labels' => $displayLabels->values()->toArray(),
            'amounts' => $data->values()->toArray(),
            'maxAmount' => $max,
            'todayIndex' => $todayIndex,
        ];
    }

    // ──────────────────────────────────────────────────────────
    // Helpers: label builders
    // ──────────────────────────────────────────────────────────

    /**
     * Minggu-minggu dalam bulan berjalan.
     * Label: Mg 1, Mg 2, …
     * Key  : W-Y  (mis. "24-2025")
     */
    private function buildWeeklyLabels(): array
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $weeks = [];
        $seen = [];
        $num = 1;

        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $key = $d->format('W') . '-' . $d->format('Y');
            if (!in_array($key, $seen, true)) {
                $seen[] = $key;
                $weeks[] = ['key' => $key, 'display' => 'Mg ' . $num++];
            }
        }

        return $weeks;
    }

    /**
     * Bulan dari bulan pertama transaksi user sampai bulan ini.
     * Label: Jan 25, Feb 25, …
     * Key  : Y-m  (mis. "2025-01")
     */
    private function buildMonthlyLabels(int $userId): array
    {
        $start = $this->firstTransactionMonth($userId);
        $end = Carbon::now()->startOfMonth();
        $months = [];

        for ($d = $start->copy(); $d->lte($end); $d->addMonth()) {
            $months[] = [
                'key' => $d->format('Y-m'),
                'display' => $d->translatedFormat('M y'),
            ];
        }

        return $months;
    }

    /**
     * Cari bulan pertama user punya transaksi (income atau expense).
     * Fallback ke bulan saat ini jika belum ada sama sekali.
     */
    private function firstTransactionMonth(int $userId): Carbon
    {
        $firstIncome = Income::where('user_id', $userId)->orderBy('created_at')->value('created_at');

        $firstExpense = Expense::where('user_id', $userId)->orderBy('created_at')->value('created_at');

        $dates = array_filter([$firstIncome, $firstExpense]);

        if (empty($dates)) {
            return Carbon::now()->startOfMonth();
        }

        $earliest = min(array_map(fn($d) => Carbon::parse($d), $dates));

        return $earliest->startOfMonth();
    }
};
?>

{{--
    wire:poll.30s  → komponen ini di-refresh otomatis setiap 30 detik
    sehingga saldo, transaksi, dan chart selalu up-to-date tanpa reload manual.
--}}
<div wire:poll.30s>

    {{-- ===== Top App Bar ===== --}}
    @php
        $authUser = Auth::user();
        $avatarUrl = $authUser->avatar ? Storage::url($authUser->avatar) : null;
        // Ambil inisial: maks 2 huruf dari kata pertama & kedua nama
        $nameParts = explode(' ', trim($authUser->name));
        $initials = strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
    @endphp
    <header
        class="w-full sticky top-0 z-50 bg-[#f9f9ff] border-b border-[#e1e2e9] flex justify-between items-center px-4 py-3 shadow-sm">
        <div class="flex items-center gap-3">
            @if ($avatarUrl)
                <div class="avatar">
                    <div class="w-10 h-10 rounded-full ring ring-primary ring-offset-1 overflow-hidden">
                        <img alt="Foto Profil" src="{{ $avatarUrl }}"
                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        {{-- fallback inisial jika URL gagal dimuat --}}
                        <div class="w-10 h-10 rounded-full ring ring-primary ring-offset-1 bg-primary text-white text-sm font-bold items-center justify-center"
                            style="display:none">
                            {{ $initials }}
                        </div>
                    </div>
                </div>
            @else
                {{-- Tidak ada avatar: tampilkan inisial --}}
                <div
                    class="w-10 h-10 rounded-full ring ring-primary ring-offset-1 bg-primary text-white text-sm font-bold flex items-center justify-center shrink-0 select-none">
                    {{ $initials }}
                </div>
            @endif
            <div>
                <p class="text-xs font-semibold text-primary tracking-wider uppercase">Halo,</p>
                <h1 class="text-lg font-bold text-base-content leading-tight">{{ $authUser->name }}!</h1>
            </div>
        </div>

        <button class="btn btn-ghost btn-circle text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
        </button>
    </header>

    {{-- ===== Main Content ===== --}}
    <main class="px-4 pt-5 space-y-6 max-w-2xl mx-auto pb-24">

        {{-- ===== Balance Card ===== --}}
        <section class="card bg-white shadow border border-outline-variant overflow-hidden">
            <div class="h-1.5 w-full bg-linear-to-r from-primary to-primary-dark"></div>
            <div class="card-body p-5">
                <p class="text-xs font-semibold text-primary uppercase tracking-widest mb-1">Saldo Kamu</p>
                <h2 class="text-3xl font-extrabold text-base-content mb-5 tracking-tight">
                    Rp {{ number_format($balance, 0, ',', '.') }}
                </h2>

                <div class="flex items-center gap-3">
                    {{-- Pemasukan --}}
                    <div
                        class="badge badge-outline gap-1.5 py-3 px-3 border-green-200 bg-green-50 text-green-700 font-semibold text-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </div>
                    {{-- Pengeluaran --}}
                    <div
                        class="badge badge-outline gap-1.5 py-3 px-3 border-red-200 bg-red-50 text-red-700 font-semibold text-xs">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                        </svg>
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </section>

        {{-- ===== Financial Chart ===== --}}
        <section class="space-y-3">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-base-content leading-tight">Pengeluaran</h3>
                    <p class="text-xs text-base-content/50">
                        {{ match ($period) {
                            'daily' => 'Minggu ini · ' . Carbon::now()->translatedFormat('d M Y'),
                            'weekly' => 'Per minggu · ' . Carbon::now()->translatedFormat('M Y'),
                            'monthly' => 'Per bulan · semua waktu',
                        } }}
                    </p>
                </div>

                <div class="flex items-center gap-1 bg-base-200 rounded-full p-0.5">
                    <button wire:click="$set('period', 'daily')"
                        class="text-[11px] font-semibold px-3 py-1 rounded-full transition-all duration-200 {{ $period === 'daily' ? 'bg-white text-primary shadow-sm' : 'text-base-content/60 hover:text-base-content' }}">
                        Harian
                    </button>
                    <button wire:click="$set('period', 'weekly')"
                        class="text-[11px] font-semibold px-3 py-1 rounded-full transition-all duration-200 {{ $period === 'weekly' ? 'bg-white text-primary shadow-sm' : 'text-base-content/60 hover:text-base-content' }}">
                        Mingguan
                    </button>
                    <button wire:click="$set('period', 'monthly')"
                        class="text-[11px] font-semibold px-3 py-1 rounded-full transition-all duration-200 {{ $period === 'monthly' ? 'bg-white text-primary shadow-sm' : 'text-base-content/60 hover:text-base-content' }}">
                        Bulanan
                    </button>
                </div>
            </div>

            <div class="card bg-white shadow border border-outline-variant p-5">
                @php
                    $chartAmounts = $chartData['amounts'];
                    $chartLabels = $chartData['labels'];
                    $chartMax = $chartData['maxAmount'];
                    $todayIndex = $chartData['todayIndex'];
                @endphp

                {{-- Bar chart --}}
                <div class="flex items-end justify-between h-40 gap-1.5 mb-3 relative">
                    {{-- Loading overlay saat ganti tab --}}
                    <div wire:loading.flex wire:target="$set"
                        class="absolute inset-0 bg-white/60 backdrop-blur-sm z-10 items-center justify-center rounded-lg"
                        style="display:none">
                        <span class="loading loading-spinner text-primary"></span>
                    </div>

                    @foreach ($chartAmounts as $i => $amount)
                        @php
                            $pct = $chartMax > 0 ? round(($amount / $chartMax) * 100) : 0;
                            $isToday = $i === $todayIndex;
                        @endphp
                        <div class="group relative flex flex-col items-center w-full h-full justify-end">
                            {{-- Tooltip --}}
                            <span
                                class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 text-[10px] bg-base-content text-base-100 rounded px-1.5 py-0.5 whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-20">
                                Rp {{ number_format($amount, 0, ',', '.') }}
                            </span>
                            <div class="w-full rounded-t-lg transition-all duration-500 {{ $isToday ? 'bg-primary' : 'bg-primary/20' }}"
                                style="height:{{ max($pct, $amount > 0 ? 4 : ($isToday ? 3 : 0)) }}%">
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- X-axis labels — hari ini dibold & warna primary --}}
                <div class="flex justify-between text-[10px] tracking-widest uppercase">
                    @foreach ($chartLabels as $i => $label)
                        <span
                            class="flex-1 text-center {{ $i === $todayIndex ? 'text-primary font-extrabold' : 'text-base-content/40 font-semibold' }}">
                            {{ $label }}
                        </span>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ===== Recent Transactions ===== --}}
        <section class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-base-content">Transaksi Terakhir</h3>
                <button class="text-[11px] font-semibold text-primary hover:text-primary/70 transition-colors">Lihat
                    Semua →</button>
            </div>

            <div class="space-y-2.5 relative">
                <div wire:loading.flex wire:target="$set"
                    class="absolute inset-0 bg-white/60 backdrop-blur-sm z-10 items-center justify-center rounded-lg"
                    style="display:none">
                    <span class="loading loading-spinner text-primary"></span>
                </div>

                @forelse ($transactions as $trx)
                    @php $cfg = $trx['config'] @endphp
                    <div
                        class="card bg-white border border-outline-variant hover:border-primary/40 transition-colors shadow-sm">
                        <div class="card-body p-4 flex-row items-center justify-between gap-3">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 {{ $cfg['bg'] }} {{ $cfg['text'] }} rounded-xl flex items-center justify-center shrink-0">
                                    {!! $cfg['icon'] !!}
                                </div>
                                <div>
                                    <p class="font-semibold text-sm text-base-content">
                                        {{ $trx['note'] ?: $cfg['label'] }}
                                    </p>
                                    <span
                                        class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full {{ $cfg['badge_bg'] }} {{ $cfg['badge_text'] }} border {{ $cfg['badge_border'] }} mt-0.5">
                                        {{ $cfg['label'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-right">
                                @if ($trx['type_group'] === 'income')
                                    <p class="font-bold text-sm text-green-600 whitespace-nowrap">
                                        + Rp {{ number_format($trx['amount'], 0, ',', '.') }}
                                    </p>
                                @else
                                    <p class="font-bold text-sm text-red-600 whitespace-nowrap">
                                        - Rp {{ number_format($trx['amount'], 0, ',', '.') }}
                                    </p>
                                @endif
                                <p class="text-xs text-base-content/50 mt-0.5">
                                    {{ $trx['created_at']->translatedFormat('d M, H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-base-content/40">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-3 opacity-30" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-sm">Belum ada transaksi</p>
                    </div>
                @endforelse
            </div>
        </section>

    </main>
</div>
