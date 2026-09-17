<?php

use Livewire\Component;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Wallet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

new class extends Component {

    public function with(): array
    {
        $userId = Auth::id();

        // ── Statistik ringkas sistem (semua user, bukan per-user) ──
        $totalUsers = User::where('id', '!=', $userId)->count();
        $newUsersToday = User::where('id', '!=', $userId)->whereDate('created_at', Carbon::today())->count();
        $totalBalance = (float) Wallet::where('user_id', '!=', $userId)->sum('balance');
        $totalIncome = (float) Income::where('user_id', '!=', $userId)->sum('amount');
        $totalExpense = (float) Expense::where('user_id', '!=', $userId)->sum('amount');

        // ── User terbaru daftar ──
        $latestUsers = User::where('id', '!=', $userId)->orderByDesc('created_at')->limit(5)->get();

        // ── Transaksi terakhir lintas semua user ──
        $incomes = Income::with('user')
            ->where('user_id', '!=', $userId)
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
            ->where('user_id', '!=', $userId)
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
        );
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