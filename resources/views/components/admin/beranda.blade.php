<?php

use Livewire\Component;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Wallet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public string $period = 'daily';

    /**
     * Live polling: refresh data setiap 30 detik otomatis.
     *
     * CATATAN ASUMSI (sesuaikan dengan struktur project kamu):
     * - Halaman ini diasumsikan sudah dilindungi middleware admin
     *   (mis. 'auth', 'can:admin' / role check) di route definition,
     *   BUKAN di dalam komponen ini.
     * - Model User diasumsikan punya kolom 'name' dan 'created_at'.
     * - Route 'admin.users.show' & 'admin.transactions.index' diasumsikan
     *   ada. Kalau belum, tinggal ganti/hapus route() di bawah.
     */

    public function with(): array
    {
        [$startDate, $labels, $groupFormat] = match ($this->period) {
            'weekly' => [
                Carbon::now()->startOfMonth(),
                $this->buildWeeklyLabels(),
                'W-Y',
            ],
            'monthly' => [
                $this->firstTransactionMonth(),
                $this->buildMonthlyLabels(),
                'Y-m',
            ],
            default => [
                Carbon::now()->startOfWeek(Carbon::MONDAY),
                ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'],
                'N',
            ],
        };

        $chartData = $this->buildChartData($this->period, $startDate, $labels, $groupFormat);

        // ── Statistik ringkas sistem (semua user, bukan per-user) ──
        $totalUsers = User::count();
        $newUsersToday = User::whereDate('created_at', Carbon::today())->count();
        $totalBalance = (float) Wallet::sum('balance');
        $totalIncome = (float) Income::sum('amount');
        $totalExpense = (float) Expense::sum('amount');

        // ── User terbaru daftar ──
        $latestUsers = User::orderByDesc('created_at')->limit(5)->get();

        // ── Transaksi terakhir lintas semua user ──
        $incomes = Income::with('user')
            ->orderByDesc('date')
            ->limit(10)
            ->get()
            ->map(
                fn($i) => [
                    'type_group' => 'income',
                    'type' => $i->type,
                    'formatted_amount' => $i->formattedAmount(),
                    'note' => $i->note,
                    'date' => $i->date,
                    'created_at' => $i->created_at,
                    'config' => Income::getTypeConfig($i->type),
                    'user_name' => $i->user->name ?? '—',
                ],
            );

        $expenses = Expense::with('user')
            ->orderByDesc('date')
            ->limit(10)
            ->get()
            ->map(
                fn($e) => [
                    'type_group' => 'expense',
                    'type' => $e->type,
                    'formatted_amount' => $e->formattedAmount(),
                    'note' => $e->note,
                    'date' => $e->date,
                    'created_at' => $e->created_at,
                    'config' => Expense::getTypeConfig($e->type),
                    'user_name' => $e->user->name ?? '—',
                ],
            );

        $transactions = $incomes
            ->concat($expenses)
            ->sortBy([
                fn($a, $b) => $b['date'] <=> $a['date'],
                fn($a, $b) => $b['created_at'] <=> $a['created_at'],
            ])
            ->take(10)
            ->values();

        return compact(
            'totalUsers',
            'newUsersToday',
            'totalBalance',
            'totalIncome',
            'totalExpense',
            'latestUsers',
            'transactions',
            'chartData',
        );
    }

    // ──────────────────────────────────────────────────────────
    // Helpers: chart (pengeluaran sistem, bukan per-user)
    // ──────────────────────────────────────────────────────────

    private function buildChartData(string $period, Carbon $startDate, array $labels, string $groupFormat): array
    {
        $endDate = match ($period) {
            'monthly' => Carbon::now()->endOfMonth(),
            'weekly' => Carbon::now()->endOfMonth(),
            default => Carbon::now()->endOfWeek(Carbon::SUNDAY),
        };

        $rawExpenses = Expense::whereBetween('created_at', [$startDate, $endDate])->get();

        $grouped = $rawExpenses->groupBy(fn($e) => $e->created_at->format($groupFormat));

        if ($period === 'daily') {
            $data = collect(range(1, 7))->map(fn($d) => (int) ($grouped->get((string) $d)?->sum('amount') ?? 0));
            $displayLabels = collect($labels);
        } else {
            $keys = collect($labels)->pluck('key');
            $data = $keys->map(fn($k) => (int) ($grouped->get($k)?->sum('amount') ?? 0));
            $displayLabels = collect($labels)->pluck('display');
        }

        $max = $data->max() ?: 1;

        $todayIndex = match ($period) {
            'daily' => (int) Carbon::now()->format('N') - 1,
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

    private function buildMonthlyLabels(): array
    {
        $start = $this->firstTransactionMonth();
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
     * Bulan pertama ada transaksi di seluruh sistem (semua user).
     */
    private function firstTransactionMonth(): Carbon
    {
        $firstIncome = Income::orderBy('date')->value('date');
        $firstExpense = Expense::orderBy('date')->value('date');

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
    wire:poll.30s → komponen ini di-refresh otomatis setiap 30 detik
    sehingga statistik, user baru, transaksi, dan chart selalu up-to-date.
--}}
<div wire:poll.30s>

    {{-- ===== Main Content ===== --}}
    <main class="px-4 pt-5 space-y-6 max-w-2xl mx-auto pb-24">

        {{-- ===== Stat Cards (2x2 grid, mobile-first) ===== --}}
        <section class="grid grid-cols-2 gap-3 page-fade" style="--delay: 0.05s">

            {{-- Total Pengguna --}}
            <div class="card bg-white shadow border border-transparent">
                <div class="card-body p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[18px]">group</span>
                        </div>
                        <p class="text-[11px] font-semibold text-base-content/50 uppercase tracking-wide">Pengguna</p>
                    </div>
                    <h3 class="text-xl font-extrabold text-base-content leading-tight">{{ $totalUsers }}</h3>
                    <p class="text-[11px] text-base-content/40 mt-0.5">
                        +{{ $newUsersToday }} baru hari ini
                    </p>
                </div>
            </div>

            {{-- Total Saldo Sistem --}}
            <div class="card bg-white shadow border border-transparent">
                <div class="card-body p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[18px]">account_balance_wallet</span>
                        </div>
                        <p class="text-[11px] font-semibold text-base-content/50 uppercase tracking-wide">Total Saldo</p>
                    </div>
                    <h3 class="text-xl font-extrabold text-base-content leading-tight">
                        Rp {{ number_format($totalBalance, 0, ',', '.') }}
                    </h3>
                    <p class="text-[11px] text-base-content/40 mt-0.5">Seluruh wallet user</p>
                </div>
            </div>

            {{-- Total Pemasukan --}}
            <div class="card bg-white shadow border border-transparent">
                <div class="card-body p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[18px]">trending_up</span>
                        </div>
                        <p class="text-[11px] font-semibold text-base-content/50 uppercase tracking-wide">Pemasukan</p>
                    </div>
                    <h3 class="text-xl font-extrabold text-green-600 leading-tight">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </h3>
                    <p class="text-[11px] text-base-content/40 mt-0.5">Akumulasi semua user</p>
                </div>
            </div>

            {{-- Total Pengeluaran --}}
            <div class="card bg-white shadow border border-transparent">
                <div class="card-body p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[18px]">trending_down</span>
                        </div>
                        <p class="text-[11px] font-semibold text-base-content/50 uppercase tracking-wide">Pengeluaran</p>
                    </div>
                    <h3 class="text-xl font-extrabold text-rose-600 leading-tight">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </h3>
                    <p class="text-[11px] text-base-content/40 mt-0.5">Akumulasi semua user</p>
                </div>
            </div>
        </section>

        {{-- ===== Financial Chart (Sistem) ===== --}}
        {{-- <section class="space-y-3 page-fade" style="--delay: 0.1s">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-base-content leading-tight">Pengeluaran Sistem</h3>
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

                <div class="flex items-end justify-between h-40 gap-1.5 mb-3 relative">
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
                        <div tabindex="0" class="group relative flex flex-col items-center w-full h-full justify-end cursor-pointer focus:outline-none">
                            <span
                                class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 text-[10px] bg-base-content text-base-100 rounded px-1.5 py-0.5 whitespace-nowrap opacity-0 group-hover:opacity-100 group-focus:opacity-100 transition-opacity pointer-events-none z-20">
                                Rp {{ number_format($amount, 0, ',', '.') }}
                            </span>
                            <div class="w-full rounded-t-lg transition-all duration-500 {{ $isToday ? 'bg-rose-500' : 'bg-rose-200' }}"
                                style="height:{{ max($pct, $amount > 0 ? 4 : ($isToday ? 3 : 0)) }}%">
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between text-[10px] tracking-widest uppercase">
                    @foreach ($chartLabels as $i => $label)
                        <span
                            class="flex-1 text-center {{ $i === $todayIndex ? 'text-primary font-extrabold' : 'text-base-content/40 font-semibold' }}">
                            {{ $label }}
                        </span>
                    @endforeach
                </div>
            </div>
        </section> --}}

        {{-- ===== Pengguna Terbaru ===== --}}
        <section class="space-y-3 page-fade" style="--delay: 0.15s">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-base-content">Pengguna Terbaru</h3>
                {{-- Sesuaikan nama route index user admin kamu --}}
                <a href="{{ route('admin.user.index') }}"
                    class="text-[11px] font-semibold text-primary hover:text-primary/70 transition-colors flex items-center">
                    Lihat Semua ➝
                </a>
            </div>

            <div class="space-y-2.5">
                @forelse ($latestUsers as $u)
                    <div class="card bg-white border border-transparent shadow-sm">
                        <div class="card-body p-4 flex-row items-center justify-between gap-3">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shrink-0">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-sm text-base-content">{{ $u->name }}</p>
                                    <p class="text-xs text-base-content/50">{{ $u->email }}</p>
                                </div>
                            </div>
                            <p class="text-xs text-base-content/40 whitespace-nowrap">
                                {{ $u->created_at->translatedFormat('d M Y') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-base-content/40">
                        <span class="material-symbols-outlined text-5xl mx-auto mb-3 opacity-30">person_off</span>
                        <p class="text-sm">Belum ada pengguna</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- ===== Transaksi Terakhir (semua user) ===== --}}
        <section class="space-y-3 page-fade" style="--delay: 0.2s">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-bold text-base-content">Transaksi Terakhir</h3>
                {{-- Sesuaikan nama route index transaksi admin kamu --}}
                <a href="{{ route('admin.transaction.index') }}"
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
                    <div class="card bg-white border border-transparent shadow-sm">
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
                                    <p class="text-[11px] text-base-content/50">
                                        {{ $trx['user_name'] }}
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
                    </div>
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