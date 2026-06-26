<?php

use Livewire\Component;
use Livewire\Attributes\Computed;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

new class extends Component {
    public int $userId;
    public string $filter = 'semua'; // semua | pemasukan | pengeluaran
    public string $filterMonth;
    public string $filterYear;
    public function mount(int $user): void
    {
        $this->userId = $user;
        $this->filterMonth = Carbon::now()->format('m');
        $this->filterYear = Carbon::now()->format('Y');
    }

    public function setFilter(string $filter): void
    {
        $this->filter = $filter;
    }

    #[Computed]
    public function incomeTotal(): float
    {
        return Income::where('user_id', $this->userId)->whereYear('date', $this->filterYear)->whereMonth('date', $this->filterMonth)->sum('amount');
    }

    #[Computed]
    public function expenseTotal(): float
    {
        return Expense::where('user_id', $this->userId)->whereYear('date', $this->filterYear)->whereMonth('date', $this->filterMonth)->sum('amount');
    }

    #[Computed]
    public function groupedTransactions(): array
    {
        $year = $this->filterYear;
        $month = $this->filterMonth;

        $transactions = collect();

        if ($this->filter !== 'pengeluaran') {
            Income::where('user_id', $this->userId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->orderByDesc('date')
                ->orderByDesc('created_at')
                ->get()
                ->each(function ($item) use (&$transactions) {
                    $transactions->push([
                        'id' => $item->id,
                        'kind' => 'income',
                        'type' => $item->type,
                        'amount' => $item->amount,
                        'formatted_amount' => $item->formattedAmount(),
                        'note' => $item->note,
                        'date' => Carbon::parse($item->date),
                        'config' => Income::$typeConfig[$item->type] ?? Income::$typeConfig['other'],
                        'route' => route('incomes.show', $item->id),
                    ]);
                });
        }

        if ($this->filter !== 'pemasukan') {
            Expense::where('user_id', $this->userId)
                ->whereYear('date', $year)
                ->whereMonth('date', $month)
                ->orderByDesc('date')
                ->orderByDesc('created_at')
                ->get()
                ->each(function ($item) use (&$transactions) {
                    $transactions->push([
                        'id' => $item->id,
                        'kind' => 'expense',
                        'type' => $item->type,
                        'amount' => $item->amount,
                        'formatted_amount' => $item->formattedAmount(),
                        'note' => $item->note,
                        'date' => Carbon::parse($item->date),
                        'config' => Expense::$typeConfig[$item->type] ?? Expense::$typeConfig['other'],
                        'route' => route('expenses.show', $item->id),
                    ]);
                });
        }

        // Sort by date desc
        $sorted = $transactions->sortByDesc(fn($t) => $t['date']->timestamp)->values();

        // Group by date string (Y-m-d)
        $grouped = [];
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        foreach ($sorted as $t) {
            $dateKey = $t['date']->format('Y-m-d');

            if (!isset($grouped[$dateKey])) {
                $dateCarbon = $t['date']->copy()->startOfDay();
                if ($dateCarbon->equalTo($today)) {
                    $label = 'Hari Ini';
                    $sublabel = $t['date']->translatedFormat('j M');
                } elseif ($dateCarbon->equalTo($yesterday)) {
                    $label = 'Kemarin';
                    $sublabel = $t['date']->translatedFormat('j M');
                } else {
                    $label = $t['date']->translatedFormat('l'); // nama hari
                    $sublabel = $t['date']->translatedFormat('j M');
                }
                $grouped[$dateKey] = [
                    'label' => $label,
                    'sublabel' => $sublabel,
                    'items' => [],
                ];
            }
            $grouped[$dateKey]['items'][] = $t;
        }

        return $grouped;
    }
};
?>

<div>
    {{-- Page Header: Riwayat --}}
    {{-- <header
        class="w-full sticky top-0 z-50 bg-[#f9f9ff]/80 backdrop-blur-md border-b border-[#e1e2e9] px-4 pt-[max(0.875rem,env(safe-area-inset-top))] pb-3.5">
        <h1 class="text-[19px] font-bold text-base-content leading-tight">Riwayat</h1>
    </header> --}}
    <main class="px-4 pt-4 space-y-5 max-w-xl mx-auto">
        <h1 class="text-xl font-bold tracking-tight">Riwayat</h1>
        {{-- Filter Controls --}}
        <div class="space-y-3 page-fade" style="--delay: 0s">

            {{-- Month & Year Selectors --}}
            <div class="flex items-center gap-2">
                <select wire:model.live="filterMonth"
                    class="select select-bordered select-sm w-[140px] bg-white focus:outline-none focus:border-primary font-semibold text-[#4b5f80]">
                    <option value="01">Januari</option>
                    <option value="02">Februari</option>
                    <option value="03">Maret</option>
                    <option value="04">April</option>
                    <option value="05">Mei</option>
                    <option value="06">Juni</option>
                    <option value="07">Juli</option>
                    <option value="08">Agustus</option>
                    <option value="09">September</option>
                    <option value="10">Oktober</option>
                    <option value="11">November</option>
                    <option value="12">Desember</option>
                </select>
                <select wire:model.live="filterYear"
                    class="select select-bordered select-sm w-[100px] bg-white focus:outline-none focus:border-primary font-semibold text-[#4b5f80]">
                    <option value="2026">2026</option>
                    <option value="2027">2027</option>
                </select>
            </div>

            {{-- Tab Filter --}}
            <div class="flex gap-1.5 w-full bg-base-200 p-1 rounded-2xl border border-base-300/30">
                <button wire:click="setFilter('semua')"
                    class="flex-1 btn btn-sm rounded-xl transition-all
                        {{ $filter === 'semua' ? 'btn-primary shadow-sm' : 'btn-ghost text-[#4b5f80]' }}"
                    id="tab-semua">Semua</button>
                <button wire:click="setFilter('pemasukan')"
                    class="flex-1 btn btn-sm rounded-xl transition-all
                        {{ $filter === 'pemasukan' ? 'btn-primary shadow-sm' : 'btn-ghost text-[#4b5f80]' }}"
                    id="tab-pemasukan">Pemasukan</button>
                <button wire:click="setFilter('pengeluaran')"
                    class="flex-1 btn btn-sm rounded-xl transition-all
                        {{ $filter === 'pengeluaran' ? 'btn-primary shadow-sm' : 'btn-ghost text-[#4b5f80]' }}"
                    id="tab-pengeluaran">Pengeluaran</button>
            </div>

            <div class="flex items-center gap-3 w-full">
                {{-- Pemasukan --}}
                <div
                    class="flex-1 badge badge-outline gap-1.5 py-4 px-3 border-green-200 bg-green-50 text-green-700 font-semibold text-xs justify-center rounded-2xl">
                    <span class="material-symbols-outlined text-[16px]">trending_up</span>
                    Rp {{ number_format($this->incomeTotal, 0, ',', '.') }}
                </div>
                {{-- Pengeluaran --}}
                <div
                    class="flex-1 badge badge-outline gap-1.5 py-4 px-3 border-rose-200 bg-rose-50 text-rose-600 font-semibold text-xs justify-center rounded-2xl">
                    <span class="material-symbols-outlined text-[16px]">trending_down</span>
                    Rp {{ number_format($this->expenseTotal, 0, ',', '.') }}
                </div>
            </div>
        </div>

        {{-- Transaction List --}}
        <div class="space-y-6 pb-12 page-fade" style="--delay: 0.1s">

            @forelse ($this->groupedTransactions as $dateKey => $group)
                <section class="space-y-2">

                    {{-- Date Header --}}
                    <div class="flex items-center justify-between px-1">
                        <h2 class="text-base font-bold">{{ $group['label'] }}</h2>
                        <span class="text-xs font-semibold text-[#191c21]/40">{{ $group['sublabel'] }}</span>
                    </div>

                    {{-- Items --}}
                    @foreach ($group['items'] as $tx)
                        <a href="{{ $tx['route'] }}"
                            class="group flex items-center gap-3 p-3.5 bg-white rounded-2xl border border-transparent hover:border-primary/30 shadow-sm transition-all active:scale-[0.98] cursor-pointer">

                            {{-- Icon --}}
                            <div
                                class="w-12 h-12 shrink-0 flex items-center justify-center rounded-xl {{ $tx['config']['bg'] }} {{ $tx['config']['text'] }}">
                                {!! $tx['config']['icon'] !!}
                            </div>

                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm truncate">{{ $tx['config']['label'] }}</p>
                                <p class="text-xs text-[#191c21]/40 truncate">
                                    {{ $tx['note'] ?? '-' }}
                                </p>
                            </div>

                            {{-- Amount --}}
                            <div class="text-right flex items-center gap-1">
                                @if ($tx['kind'] === 'income')
                                    <p class="font-bold text-sm text-green-700">
                                        + Rp {{ $tx['formatted_amount'] }}
                                    </p>
                                @else
                                    <p class="font-bold text-sm text-rose-600">
                                        - Rp {{ $tx['formatted_amount'] }}
                                    </p>
                                @endif
                                <span
                                    class="material-symbols-outlined text-default group-hover:text-primary transition-colors text-[20px]">chevron_right</span>
                            </div>
                        </a>
                    @endforeach

                </section>
            @empty
                {{-- Empty State --}}
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-base-200 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-3xl text-[#191c21]/30">inbox</span>
                    </div>
                    <p class="font-semibold text-sm text-[#191c21]/60">Tidak ada transaksi</p>
                    <p class="text-xs text-[#191c21]/40 mt-1">
                        Belum ada data untuk bulan ini
                    </p>
                </div>
            @endforelse

        </div>
    </main>
</div>
