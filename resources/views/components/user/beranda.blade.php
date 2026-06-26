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
        $balance = $wallet ? (float) $wallet->balance : 0;

        $incomes = Income::where('user_id', $user->id)->orderByDesc('date')->limit(10)->get()->map(
            fn($i) => [
                'type_group' => 'income',
                'type' => $i->type,
                'amount' => $i->amount,
                'formatted_amount' => $i->formattedAmount(),
                'note' => $i->note,
                'date' => $i->date,
                'created_at' => $i->created_at,
                'config' => Income::getTypeConfig($i->type),
                'route' => route('incomes.show', $i->id),
            ],
        );

        $expenses = Expense::where('user_id', $user->id)->orderByDesc('date')->limit(10)->get()->map(
            fn($e) => [
                'type_group' => 'expense',
                'type' => $e->type,
                'amount' => $e->amount,
                'formatted_amount' => $e->formattedAmount(),
                'note' => $e->note,
                'date' => $e->date,
                'created_at' => $e->created_at,
                'config' => Expense::getTypeConfig($e->type),
                'route' => route('expenses.show', $e->id),
            ],
        );

        $transactions = $incomes
            ->concat($expenses)
            ->sortBy([
                fn($a, $b) => $b['date'] <=> $a['date'], // 1. tanggal terbaru
                fn($a, $b) => $b['created_at'] <=> $a['created_at'], // 2. data terbaru (jika tanggal sama)
            ])
            ->take(10)
            ->values();

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
        $firstIncome = Income::where('user_id', $userId)->orderBy('date')->value('date');

        $firstExpense = Expense::where('user_id', $userId)->orderBy('date')->value('date');

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



    {{-- ===== Main Content ===== --}}
    <main class="px-4 pt-5 space-y-6 max-w-2xl mx-auto pb-24">

        {{-- ===== Balance Card ===== --}}
        <section class="card bg-white shadow overflow-hidden border border-transparent page-fade" style="--delay: 0s">
            <div class="h-1.5 w-full bg-linear-to-r from-primary to-primary-dark"></div>
            <div class="card-body p-5">
                <p class="text-xs font-semibold text-primary uppercase tracking-widest mb-1">Saldo Kamu</p>
                <h2 class="text-3xl font-extrabold text-base-content mb-5 tracking-tight">
                    Rp {{ number_format($balance, 0, ',', '.') }}
                </h2>
{{-- balance tetap pakai number_format karena bukan dari model langsung --}}

                <div class="flex items-center gap-3">
                    {{-- Pemasukan --}}
                    <div
                        class="badge badge-outline gap-1.5 py-3 px-3 border-green-200 bg-green-50 text-green-700 font-semibold text-xs">
                        <span class="material-symbols-outlined text-[16px]">trending_up</span>
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </div>
                    {{-- Pengeluaran --}}
                    <div
                        class="badge badge-outline gap-1.5 py-3 px-3 border-rose-200 bg-rose-50 text-rose-600 font-semibold text-xs">
                        <span class="material-symbols-outlined text-[16px]">trending_down</span>
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </div>
{{-- totalIncome & totalExpense = agregasi DB (bukan instance model), tetap pakai number_format --}}
                </div>
            </div>
        </section>

        {{-- ===== Financial Chart ===== --}}
        <section class="space-y-3 page-fade" style="--delay: 0.1s">
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

            <div class="card bg-white shadow p-5 border border-transparent">
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
{{-- $amount pada chart = integer mentah dari aggregasi, bukan instance model --}}
                            <div class="w-full rounded-t-lg transition-all duration-500 {{ $isToday ? 'bg-rose-500' : 'bg-rose-200' }}"
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
        <section class="space-y-3 page-fade" style="--delay: 0.2s">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-base-content">Transaksi Terakhir</h3>
                <a href="{{ route('history') }}"
                    class="text-[11px] font-semibold text-primary hover:text-primary/70 transition-colors flex items-center">
                    Lihat Semua ➝
                </a>
            </div>

            <div class="space-y-2.5 relative">
                <div wire:loading.flex wire:target="$set"
                    class="absolute inset-0 bg-white/60 backdrop-blur-sm z-10 items-center justify-center rounded-lg"
                    style="display:none">
                    <span class="loading loading-spinner text-primary"></span>
                </div>

                @forelse ($transactions as $trx)
                    @php $cfg = $trx['config'] @endphp
                    <a href="{{ $trx['route'] }}"
                        class="card bg-white border border-transparent hover:border-primary/40 transition-colors shadow-sm">
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
                                        + Rp {{ $trx['formatted_amount'] }}
                                    </p>
                                @else
                                    <p class="font-bold text-sm text-rose-600 whitespace-nowrap">
                                        - Rp {{ $trx['formatted_amount'] }}
                                    </p>
                                @endif
                                <p class="text-xs text-base-content/50 mt-0.5">
                                    {{ $trx['date']->translatedFormat('d M Y') }}
                                </p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-10 text-base-content/40">
                        <span class="material-symbols-outlined text-5xl mx-auto mb-3 opacity-30">inbox</span>
                        <p class="text-sm">Belum ada transaksi</p>
                    </div>
                @endforelse
            </div>
        </section>

    </main>
</div>
