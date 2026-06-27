<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Services\StatisticsService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

new class extends Component {
    public int $month;
    public int $year;

    public function mount(int $month, int $year): void
    {
        $this->month = $month;
        $this->year = $year;
    }

    // ──────────────────────────────────────────────────────────
    // Computed Properties
    // ──────────────────────────────────────────────────────────

    #[Computed]
    public function monthLabel(): string
    {
        return Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y');
    }

    #[Computed]
    public function isCurrentMonth(): bool
    {
        return Carbon::create($this->year, $this->month, 1)->format('Y-m') === now()->format('Y-m');
    }

    #[Computed]
    public function cashFlowChart(): array
    {
        return app(StatisticsService::class)->getCashFlowChartData(Auth::id(), 'mingguan', $this->month, $this->year);
    }

    #[Computed]
    public function categoryBreakdown(): array
    {
        return app(StatisticsService::class)->getCategoryBreakdown(Auth::id(), 'bulanan', $this->month, $this->year);
    }

    #[Computed]
    public function summaryCards(): array
    {
        return app(StatisticsService::class)->getSummaryCards(Auth::id(), $this->month, $this->year);
    }

    #[Computed]
    public function comparisonSummary(): array
    {
        return app(StatisticsService::class)->getComparisonSummary(Auth::id(), 'bulanan', $this->month, $this->year);
    }
};
?>

<div>
    <main class="px-4 pt-6 max-w-2xl mx-auto space-y-6 pb-8">

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- HEADER                                            --}}
        {{-- ══════════════════════════════════════════════════ --}}
        <div class="flex flex-col gap-1 stat-fade" style="--delay:0s">
            {{-- Back button --}}
            <a href="{{ route('statistic') }}"
                class="inline-flex items-center gap-1 text-xs font-semibold text-[#191c21]/50 hover:text-primary transition-colors mb-1 w-fit">
                <span class="material-symbols-outlined text-[16px]">arrow_back_ios</span>
                Kembali ke Statistik
            </a>

            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl font-bold tracking-tight">{{ $this->monthLabel }}</h1>
                    <p class="text-xs text-[#191c21]/40 mt-0.5">Detail statistik keuangan</p>
                </div>
                @if ($this->isCurrentMonth)
                    <span class="badge bg-primary text-white border-0 font-bold px-3 py-2 text-xs">Berjalan</span>
                @endif
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- 1. KARTU PERBANDINGAN PENGELUARAN                 --}}
        {{-- ══════════════════════════════════════════════════ --}}
        @php $cmp = $this->comparisonSummary; @endphp

        <div class="card bg-white border border-base-300/40 shadow-sm stat-fade" style="--delay:0.05s">
            <div class="card-body gap-3">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-semibold text-[#191c21]/50 mb-1">{{ $cmp['period_label'] }}</p>
                        <h2 class="text-xl font-bold">
                            Rp {{ number_format($cmp['current_total'], 0, ',', '.') }}
                        </h2>
                    </div>

                    @if ($cmp['current_total'] > 0 || $cmp['previous_total'] > 0)
                        <div
                            class="badge gap-1 py-3 px-3 font-semibold
                            {{ $cmp['is_increase']
                                ? 'bg-rose-100 text-rose-700 border-rose-200'
                                : 'bg-emerald-100 text-emerald-700 border-emerald-200' }}">
                            <span class="material-symbols-outlined text-[16px]">
                                {{ $cmp['is_increase'] ? 'trending_up' : 'trending_down' }}
                            </span>
                            {{ $cmp['percentage_change'] }}%
                        </div>
                    @endif
                </div>

                <progress class="progress progress-error w-full" value="{{ $cmp['progress_value'] }}"
                    max="100"></progress>

                <p class="text-xs text-[#191c21]/50 italic">
                    @if ($cmp['top_category'])
                        Pengeluaran terbesar di kategori
                        <span class="font-semibold text-primary">{{ $cmp['top_category'] }}</span>
                    @elseif($cmp['current_total'] == 0)
                        Belum ada pengeluaran pada bulan ini
                    @else
                        Tidak ada data pembanding dari bulan sebelumnya
                    @endif
                </p>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- 2. BENTO SUMMARY CARDS                            --}}
        {{-- ══════════════════════════════════════════════════ --}}
        @php $cards = $this->summaryCards; @endphp

        <section class="stat-fade" style="--delay:0.1s">
            <h3 class="font-bold text-base mb-3">Ringkasan Keuangan</h3>
            <div class="grid grid-cols-2 gap-3">

                {{-- Total Saldo (selalu real-time) --}}
                <div class="card col-span-2 shadow-sm overflow-hidden"
                    style="background: linear-gradient(135deg, #005bac 0%, #003b73 100%)">
                    <div class="card-body p-4 gap-1 flex-row items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-white/60 mb-0.5">Total Saldo</p>
                            <p class="text-2xl font-extrabold text-white leading-none">
                                Rp {{ number_format($cards['balance'], 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-white/50 mt-1">Saldo dompet saat ini</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-[28px]">account_balance_wallet</span>
                        </div>
                    </div>
                </div>

                {{-- Total Pemasukan --}}
                <div class="card bg-white border border-base-300/40 shadow-sm">
                    <div class="card-body p-4 gap-1">
                        <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center mb-1">
                            <span class="material-symbols-outlined text-primary text-[20px]">arrow_circle_up</span>
                        </div>
                        <p class="text-[11px] font-bold text-[#191c21]/40">Pemasukan</p>
                        <p class="text-base font-extrabold text-primary leading-tight">
                            Rp {{ number_format($cards['total_income'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- Total Pengeluaran --}}
                <div class="card bg-rose-50 border border-rose-200/60 shadow-sm">
                    <div class="card-body p-4 gap-1">
                        <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center mb-1">
                            <span class="material-symbols-outlined text-rose-600 text-[20px]">arrow_circle_down</span>
                        </div>
                        <p class="text-[11px] font-bold text-rose-700/60">Pengeluaran</p>
                        <p class="text-base font-extrabold text-rose-700 leading-tight">
                            Rp {{ number_format($cards['total_expense'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- Net (selisih) --}}
                @php $net = $cards['total_income'] - $cards['total_expense']; @endphp
                <div
                    class="card col-span-2 border shadow-sm {{ $net >= 0 ? 'bg-emerald-50 border-emerald-200/60' : 'bg-rose-50 border-rose-200/60' }}">
                    <div class="card-body p-4 flex-row items-center justify-between">
                        <div>
                            <p
                                class="text-[11px] font-bold {{ $net >= 0 ? 'text-emerald-700/60' : 'text-rose-700/60' }}">
                                {{ $net >= 0 ? 'Surplus Bulan Ini' : 'Defisit Bulan Ini' }}
                            </p>
                            <p
                                class="text-xl font-extrabold {{ $net >= 0 ? 'text-emerald-700' : 'text-rose-700' }} leading-tight">
                                {{ $net >= 0 ? '+' : '' }}Rp {{ number_format(abs($net), 0, ',', '.') }}
                            </p>
                        </div>
                        <div
                            class="w-10 h-10 rounded-2xl {{ $net >= 0 ? 'bg-emerald-100' : 'bg-rose-100' }} flex items-center justify-center">
                            <span
                                class="material-symbols-outlined {{ $net >= 0 ? 'text-emerald-600' : 'text-rose-600' }} text-[22px]">
                                {{ $net >= 0 ? 'balance' : 'trending_down' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- 3. BAR CHART ARUS KAS (per Minggu)                --}}
        {{-- ══════════════════════════════════════════════════ --}}
        @php $chart = $this->cashFlowChart; @endphp

        <section class="card bg-white border border-base-300/40 shadow-sm stat-fade" style="--delay:0.15s">
            <div class="card-body gap-4">

                {{-- Header --}}
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-base">Arus Kas</h3>
                        <p class="text-[11px] text-[#191c21]/40 mt-0.5">Per minggu dalam bulan ini</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex items-center gap-1.5">
                            <div class="w-3 h-3 rounded-sm bg-primary"></div>
                            <span class="text-[11px] font-semibold text-[#191c21]/60">Masuk</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <div class="w-3 h-3 rounded-sm bg-rose-400"></div>
                            <span class="text-[11px] font-semibold text-[#191c21]/60">Keluar</span>
                        </div>
                    </div>
                </div>

                @if (empty($chart['labels']))
                    <div class="flex flex-col items-center justify-center h-32 text-[#191c21]/30 gap-2">
                        <span class="material-symbols-outlined text-[36px]">bar_chart</span>
                        <p class="text-xs font-medium">Belum ada data</p>
                    </div>
                @else
                    {{-- Bar Chart --}}
                    <div>
                        {{-- Bars --}}
                        <div class="flex items-end justify-between h-44 gap-2 px-1 relative">
                            {{-- Loading overlay saat render ulang (jika ada) --}}
                            <div wire:loading.flex
                                class="absolute inset-0 bg-white/60 backdrop-blur-sm z-10 items-center justify-center rounded-lg"
                                style="display:none">
                                <span class="loading loading-spinner text-primary"></span>
                            </div>

                            @foreach ($chart['labels'] as $i => $label)
                                @php
                                    $inH = $chart['income'][$i] ?? 0;
                                    $exH = $chart['expense'][$i] ?? 0;
                                    $inR = $chart['income_raw'][$i] ?? 0;
                                    $exR = $chart['expense_raw'][$i] ?? 0;
                                @endphp
                                <div class="flex-1 flex flex-col items-center h-full justify-end">
                                    <div class="flex gap-[3px] items-end w-full justify-center" style="height: 85%">
                                        {{-- Income bar --}}
                                        <div tabindex="0" class="group relative flex-1 flex items-end justify-center h-full cursor-pointer focus:outline-none">
                                            @if ($inR > 0)
                                                <span class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 text-[10px] bg-base-content text-base-100 rounded px-1.5 py-0.5 whitespace-nowrap opacity-0 group-hover:opacity-100 group-focus:opacity-100 transition-opacity pointer-events-none z-20">
                                                    Rp {{ number_format($inR, 0, ',', '.') }}
                                                </span>
                                            @endif
                                            <div class="w-full rounded-t-lg bg-primary transition-all duration-500"
                                                style="height:{{ max($inH, $inR > 0 ? 4 : 0) }}%">
                                            </div>
                                        </div>

                                        {{-- Expense bar --}}
                                        <div tabindex="0" class="group relative flex-1 flex items-end justify-center h-full cursor-pointer focus:outline-none">
                                            @if ($exR > 0)
                                                <span class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 text-[10px] bg-base-content text-base-100 rounded px-1.5 py-0.5 whitespace-nowrap opacity-0 group-hover:opacity-100 group-focus:opacity-100 transition-opacity pointer-events-none z-20">
                                                    Rp {{ number_format($exR, 0, ',', '.') }}
                                                </span>
                                            @endif
                                            <div class="w-full rounded-t-lg bg-rose-400 transition-all duration-500"
                                                style="height:{{ max($exH, $exR > 0 ? 4 : 0) }}%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Labels --}}
                        <div class="flex justify-around mt-2">
                            @foreach ($chart['labels'] as $label)
                                <span
                                    class="flex-1 text-center text-[10px] font-bold text-[#191c21]/40 uppercase tracking-wider">
                                    {{ $label }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- 4. DONUT CHART + DAFTAR KATEGORI                  --}}
        {{-- ══════════════════════════════════════════════════ --}}
        @php $categories = $this->categoryBreakdown; @endphp

        <section class="card bg-white border border-base-300/40 shadow-sm stat-fade" style="--delay:0.2s">
            <div class="card-body gap-5">
                <h3 class="font-bold text-base">Pengeluaran per Kategori</h3>

                @if (empty($categories))
                    <div class="flex flex-col items-center justify-center py-6 text-[#191c21]/30 gap-2">
                        <span class="material-symbols-outlined text-[40px]">donut_large</span>
                        <p class="text-xs font-medium">Belum ada pengeluaran pada bulan ini</p>
                    </div>
                @else
                    <div class="flex flex-col md:flex-row items-center gap-6">

                        {{-- Donut SVG --}}
                        <div class="relative w-36 h-36 shrink-0">
                            <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36" aria-hidden="true">
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                    fill="none" stroke="#e8eaf0" stroke-width="4" />

                                @foreach ($categories as $cat)
                                    <path
                                        d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                        fill="none" stroke="{{ $cat['stroke_color'] }}" stroke-width="4"
                                        stroke-dasharray="{{ $cat['dash_array'] }}"
                                        stroke-dashoffset="{{ $cat['dash_offset'] }}" stroke-linecap="butt" />
                                @endforeach
                            </svg>

                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-lg font-extrabold text-primary leading-none">
                                    {{ $categories[0]['percentage'] }}%
                                </span>
                                <span
                                    class="text-[10px] font-bold text-[#191c21]/40 tracking-wide text-center leading-tight mt-0.5 px-2">
                                    {{ $categories[0]['name'] }}
                                </span>
                            </div>
                        </div>

                        {{-- Daftar kategori --}}
                        <div class="flex-1 w-full flex flex-col gap-3">
                            @foreach ($categories as $cat)
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $cat['bg_class'] }} {{ $cat['text_class'] }}">
                                        <span class="material-symbols-outlined text-[20px]">{{ $cat['icon'] }}</span>
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-sm font-medium truncate">{{ $cat['name'] }}</span>
                                            <span class="text-sm font-bold ml-2 shrink-0">
                                                Rp {{ number_format($cat['amount'], 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <div class="h-1.5 w-full bg-base-200 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-700"
                                                style="width: {{ $cat['percentage'] }}%; background-color: {{ $cat['stroke_color'] }};">
                                            </div>
                                        </div>
                                    </div>

                                    <span class="text-xs font-semibold text-[#191c21]/40 shrink-0 w-10 text-right">
                                        {{ $cat['percentage'] }}%
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>

    </main>
</div>
