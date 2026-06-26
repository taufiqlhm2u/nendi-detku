<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Services\StatisticsService;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public string $period = 'bulanan';

    // ──────────────────────────────────────────────────────────
    // Computed Properties — otomatis di-reset saat $period berubah
    // ──────────────────────────────────────────────────────────

    #[Computed]
    public function comparisonSummary(): array
    {
        return app(StatisticsService::class)
            ->getComparisonSummary(Auth::id(), $this->period);
    }

    #[Computed]
    public function cashFlowChart(): array
    {
        return app(StatisticsService::class)
            ->getCashFlowChartData(Auth::id(), $this->period);
    }

    #[Computed]
    public function categoryBreakdown(): array
    {
        return app(StatisticsService::class)
            ->getCategoryBreakdown(Auth::id(), $this->period);
    }

    #[Computed]
    public function summaryCards(): array
    {
        return app(StatisticsService::class)
            ->getSummaryCards(Auth::id());
    }

    #[Computed]
    public function monthlyHistory(): array
    {
        return app(StatisticsService::class)
            ->getMonthlyHistory(Auth::id(), 6);
    }

    // ──────────────────────────────────────────────────────────
    // Methods
    // ──────────────────────────────────────────────────────────

    public function setPeriod(string $period): void
    {
        if (in_array($period, ['harian', 'mingguan', 'bulanan'])) {
            $this->period = $period;
        }
    }
};
?>

<div>
    <main class="px-4 pt-6 max-w-2xl mx-auto space-y-6 pb-8 relative">

        {{-- Global Loading Overlay --}}
        <div wire:loading wire:target="setPeriod" class="absolute inset-0 z-50 bg-base-100/50 backdrop-blur-sm rounded-3xl">
            <div class="fixed top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2">
                <span class="loading loading-spinner loading-lg text-primary"></span>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- 1. JUDUL + TAB TOGGLE                             --}}
        {{-- ══════════════════════════════════════════════════ --}}
        <div class="flex flex-col gap-4 stat-fade" style="--delay:0s">
            <h1 class="text-xl font-bold tracking-tight">Statistik Keuangan</h1>
            <div class="flex gap-2 w-full bg-base-200 p-1.5 rounded-2xl border border-base-300/30">

                <button id="tab-harian"
                    wire:click="setPeriod('harian')"
                    class="flex-1 btn btn-sm rounded-xl transition-all duration-200 border-0
                           {{ $period === 'harian' ? 'btn-primary shadow-sm' : 'btn-ghost text-[#4b5f80]' }}">
                    Harian
                </button>
                <button id="tab-mingguan"
                    wire:click="setPeriod('mingguan')"
                    class="flex-1 btn btn-sm rounded-xl transition-all duration-200 border-0
                           {{ $period === 'mingguan' ? 'btn-primary shadow-sm' : 'btn-ghost text-[#4b5f80]' }}">
                    Mingguan
                </button>
                <button id="tab-bulanan"
                    wire:click="setPeriod('bulanan')"
                    class="flex-1 btn btn-sm rounded-xl transition-all duration-200 border-0
                           {{ $period === 'bulanan' ? 'btn-primary shadow-sm' : 'btn-ghost text-[#4b5f80]' }}">
                    Bulanan
                </button>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- 2. KARTU PERBANDINGAN PENGELUARAN                 --}}
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

                    @if($cmp['current_total'] > 0 || $cmp['previous_total'] > 0)
                        <div class="badge gap-1 py-3 px-3 font-semibold
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

                <progress class="progress progress-primary w-full"
                          value="{{ $cmp['progress_value'] }}" max="100"></progress>

                <p class="text-xs text-[#191c21]/50 italic">
                    @if($cmp['top_category'])
                        Pengeluaran terbesar di kategori <span class="font-semibold text-primary">{{ $cmp['top_category'] }}</span>
                    @elseif($cmp['current_total'] == 0)
                        Belum ada pengeluaran pada periode ini
                    @else
                        Tidak ada data pembanding dari periode sebelumnya
                    @endif
                </p>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- 3. BAR CHART ARUS KAS                             --}}
        {{-- ══════════════════════════════════════════════════ --}}
        @php $chart = $this->cashFlowChart; @endphp

        <section class="card bg-white border border-base-300/40 shadow-sm stat-fade" style="--delay:0.1s">
            <div class="card-body gap-4">

                {{-- Header --}}
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-base">Arus Kas</h3>
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

                @if(empty($chart['labels']))
                    <div class="flex flex-col items-center justify-center h-32 text-[#191c21]/30 gap-2">
                        <span class="material-symbols-outlined text-[36px]">bar_chart</span>
                        <p class="text-xs font-medium">Belum ada data</p>
                    </div>
                @else
                    {{-- Bar Chart — Alpine.js untuk animasi --}}
                    <div
                        wire:key="barchart-{{ $this->period }}"
                        x-data="{
                            incomeH: {{ json_encode($chart['income']) }},
                            expenseH: {{ json_encode($chart['expense']) }},
                            incomeRaw: {{ json_encode($chart['income_raw']) }},
                            expenseRaw: {{ json_encode($chart['expense_raw']) }},
                            animated: false,
                            init() { this.$nextTick(() => setTimeout(() => this.animated = true, 60)); }
                        }"
                    >
                        {{-- Bars --}}
                        <div class="flex items-end justify-between h-44 gap-2 px-1">
                            @foreach($chart['labels'] as $i => $label)
                                @php
                                    $inH  = $chart['income'][$i]  ?? 0;
                                    $exH  = $chart['expense'][$i] ?? 0;
                                    $inR  = $chart['income_raw'][$i]  ?? 0;
                                    $exR  = $chart['expense_raw'][$i] ?? 0;
                                @endphp
                                <div class="flex-1 flex flex-col items-center h-full justify-end">
                                    <div class="flex gap-[3px] items-end w-full justify-center" style="height: 85%">
                                        {{-- Income bar --}}
                                        <div class="relative group flex-1 flex items-end justify-center" style="height: 100%">
                                            <div
                                                class="w-full rounded-t-lg bg-primary bar-anim"
                                                style="height: 0%; transition: height 0.6s cubic-bezier(.34,1.56,.64,1) {{ $i * 0.05 }}s;"
                                                x-bind:style="animated ? 'height: {{ $inH }}%' : 'height: 0%'"
                                            ></div>
                                            {{-- Tooltip --}}
                                            @if($inR > 0)
                                            <div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 bg-primary text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10 pointer-events-none">
                                                {{ 'Rp '.number_format($inR, 0, ',', '.') }}
                                            </div>
                                            @endif
                                        </div>

                                        {{-- Expense bar --}}
                                        <div class="relative group flex-1 flex items-end justify-center" style="height: 100%">
                                            <div
                                                class="w-full rounded-t-lg bg-rose-400 bar-anim"
                                                style="height: 0%; transition: height 0.6s cubic-bezier(.34,1.56,.64,1) {{ $i * 0.05 + 0.03 }}s;"
                                                x-bind:style="animated ? 'height: {{ $exH }}%' : 'height: 0%'"
                                            ></div>
                                            @if($exR > 0)
                                            <div class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 bg-rose-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10 pointer-events-none">
                                                {{ 'Rp '.number_format($exR, 0, ',', '.') }}
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Labels --}}
                        <div class="flex justify-around mt-2">
                            @foreach($chart['labels'] as $label)
                                <span class="flex-1 text-center text-[10px] font-bold text-[#191c21]/40 uppercase tracking-wider">
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

        <section class="card bg-white border border-base-300/40 shadow-sm stat-fade" style="--delay:0.15s">
            <div class="card-body gap-5">
                <h3 class="font-bold text-base">Pengeluaran per Kategori</h3>

                @if(empty($categories))
                    <div class="flex flex-col items-center justify-center py-6 text-[#191c21]/30 gap-2">
                        <span class="material-symbols-outlined text-[40px]">donut_large</span>
                        <p class="text-xs font-medium">Belum ada pengeluaran pada periode ini</p>
                    </div>
                @else
                    <div class="flex flex-col md:flex-row items-center gap-6">

                        {{-- Donut SVG --}}
                        <div class="relative w-36 h-36 shrink-0">
                            <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36" aria-hidden="true">
                                {{-- Track --}}
                                <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                      fill="none" stroke="#e8eaf0" stroke-width="4"/>

                                {{-- Segments --}}
                                @foreach($categories as $cat)
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none"
                                          stroke="{{ $cat['stroke_color'] }}"
                                          stroke-width="4"
                                          stroke-dasharray="{{ $cat['dash_array'] }}"
                                          stroke-dashoffset="{{ $cat['dash_offset'] }}"
                                          stroke-linecap="butt"/>
                                @endforeach
                            </svg>

                            {{-- Tengah donut: kategori terbesar --}}
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-lg font-extrabold text-primary leading-none">
                                    {{ $categories[0]['percentage'] }}%
                                </span>
                                <span class="text-[10px] font-bold text-[#191c21]/40 tracking-wide text-center leading-tight mt-0.5 px-2">
                                    {{ $categories[0]['name'] }}
                                </span>
                            </div>
                        </div>

                        {{-- Daftar kategori --}}
                        <div class="flex-1 w-full flex flex-col gap-3">
                            @foreach($categories as $cat)
                                <div class="flex items-center gap-3">
                                    {{-- Icon --}}
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $cat['bg_class'] }} {{ $cat['text_class'] }}">
                                        <span class="material-symbols-outlined text-[20px]">{{ $cat['icon'] }}</span>
                                    </div>

                                    {{-- Nama + progress --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-sm font-medium truncate">{{ $cat['name'] }}</span>
                                            <span class="text-sm font-bold ml-2 shrink-0">
                                                Rp {{ number_format($cat['amount'], 0, ',', '.') }}
                                            </span>
                                        </div>
                                        <div class="h-1.5 w-full bg-base-200 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full transition-all duration-700"
                                                 style="width: {{ $cat['percentage'] }}%; background-color: {{ $cat['stroke_color'] }};"></div>
                                        </div>
                                    </div>

                                    {{-- Persentase --}}
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

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- 5. BENTO SUMMARY CARDS (2×2)                      --}}
        {{-- ══════════════════════════════════════════════════ --}}
        @php $cards = $this->summaryCards; @endphp

        <section class="stat-fade" style="--delay:0.2s">
            <h3 class="font-bold text-base mb-3">Ringkasan Keuangan</h3>
            <div class="grid grid-cols-2 gap-3">

                {{-- Total Saldo --}}
                <div class="card col-span-2 shadow-sm overflow-hidden"
                     style="background: linear-gradient(135deg, #005bac 0%, #003b73 100%)">
                    <div class="card-body p-4 gap-1 flex-row items-center justify-between">
                        <div>
                            <p class="text-xs font-bold text-white/60 mb-0.5">Total Saldo</p>
                            <p class="text-2xl font-extrabold text-white leading-none">
                                Rp {{ number_format($cards['balance'], 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-white/50 mt-1">Saldo dompet</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center">
                            <span class="material-symbols-outlined text-white text-[28px]">account_balance_wallet</span>
                        </div>
                    </div>
                </div>

                {{-- Tabungan --}}
                <div class="card bg-white border border-base-300/40 shadow-sm">
                    <div class="card-body p-4 gap-1">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center mb-1">
                            <span class="material-symbols-outlined text-emerald-600 text-[20px]">savings</span>
                        </div>
                        <p class="text-[11px] font-bold text-[#191c21]/40">Tabungan</p>
                        <p class="text-base font-extrabold text-emerald-700 leading-tight">
                            Rp {{ number_format($cards['savings'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- Total Pemasukan Bulan Ini --}}
                <div class="card bg-white border border-base-300/40 shadow-sm">
                    <div class="card-body p-4 gap-1">
                        <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center mb-1">
                            <span class="material-symbols-outlined text-primary text-[20px]">arrow_circle_up</span>
                        </div>
                        <p class="text-[11px] font-bold text-[#191c21]/40">Masuk Bulan Ini</p>
                        <p class="text-base font-extrabold text-primary leading-tight">
                            Rp {{ number_format($cards['total_income'], 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- Total Pengeluaran Bulan Ini --}}
                <div class="card col-span-2 bg-rose-50 border border-rose-200/60 shadow-sm">
                    <div class="card-body p-4 flex-row items-center justify-between">
                        <div>
                            <p class="text-[11px] font-bold text-rose-700/60">Keluar Bulan Ini</p>
                            <p class="text-xl font-extrabold text-rose-700 leading-tight">
                                Rp {{ number_format($cards['total_expense'], 0, ',', '.') }}
                            </p>
                        </div>
                        <div class="w-10 h-10 rounded-2xl bg-rose-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-rose-600 text-[22px]">arrow_circle_down</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════ --}}
        {{-- 6. RIWAYAT BULANAN                                --}}
        {{-- Hanya tampilkan bulan yang benar-benar ada data  --}}
        {{-- ══════════════════════════════════════════════════ --}}
        @php $history = $this->monthlyHistory; @endphp

        <section class="card bg-white border border-base-300/40 shadow-sm stat-fade" style="--delay:0.25s">
            <div class="card-body gap-1 p-0 overflow-hidden">
                <div class="px-5 pt-5 pb-3 border-b border-base-300/30">
                    <h3 class="font-bold text-base">Riwayat per Bulan</h3>
                    <p class="text-xs text-[#191c21]/40 mt-0.5">Hanya bulan dengan transaksi yang ditampilkan</p>
                </div>

                @if(empty($history))
                    <div class="flex flex-col items-center justify-center py-10 text-[#191c21]/30 gap-2">
                        <span class="material-symbols-outlined text-[40px]">calendar_month</span>
                        <p class="text-xs font-medium">Belum ada riwayat transaksi</p>
                    </div>
                @else
                    <div class="divide-y divide-base-300/20">
                        @foreach($history as $row)
                            <div class="px-5 py-3.5 {{ $row['is_current'] ? 'bg-primary/5' : '' }} hover:bg-base-100/60 transition-colors">

                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-semibold {{ $row['is_current'] ? 'text-primary' : 'text-[#191c21]' }}">
                                            {{ $row['month_name'] }}
                                        </span>
                                        @if($row['is_current'])
                                            <span class="badge badge-xs bg-primary text-white border-0 font-bold px-1.5">Berjalan</span>
                                        @endif
                                    </div>

                                    @if($row['delta_pct'] != 0)
                                        <span class="text-xs font-bold px-1.5 py-0.5 rounded-lg
                                            {{ $row['delta_pct'] > 0
                                                ? 'text-rose-700 bg-rose-100'
                                                : 'text-emerald-700 bg-emerald-100' }}">
                                            {{ $row['delta_pct'] > 0 ? '+' : '' }}{{ $row['delta_pct'] }}%
                                        </span>
                                    @endif
                                </div>

                                {{-- Bar ganda: income (biru) & expense (rose) --}}
                                <div class="flex flex-col gap-1.5">
                                    {{-- Income bar --}}
                                    @if($row['total_income'] > 0)
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-bold text-[#191c21]/40 w-10 text-right shrink-0">Masuk</span>
                                        <div class="flex-1 h-2 bg-base-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-primary rounded-full transition-all duration-700"
                                                 style="width: {{ $row['income_bar'] }}%"></div>
                                        </div>
                                        <span class="text-[11px] font-semibold text-primary w-20 text-right shrink-0">
                                            Rp {{ number_format($row['total_income'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                    @endif

                                    {{-- Expense bar --}}
                                    @if($row['total_expense'] > 0)
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] font-bold text-[#191c21]/40 w-10 text-right shrink-0">Keluar</span>
                                        <div class="flex-1 h-2 bg-rose-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-rose-400 rounded-full transition-all duration-700"
                                                 style="width: {{ $row['expense_bar'] }}%"></div>
                                        </div>
                                        <span class="text-[11px] font-semibold text-rose-600 w-20 text-right shrink-0">
                                            Rp {{ number_format($row['total_expense'], 0, ',', '.') }}
                                        </span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

    </main>

    @push('styles')
    <style>
        /* ── Fade-in animasi per section ── */
        @keyframes statFadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .stat-fade {
            animation: statFadeIn 0.45s ease-out both;
            animation-delay: var(--delay, 0s);
        }
    </style>
    @endpush
</div>