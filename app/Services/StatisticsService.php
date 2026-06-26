<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StatisticsService
{
    // ──────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ──────────────────────────────────────────────────────────

    /**
     * Kembalikan rentang tanggal [currentStart, currentEnd, prevStart, prevEnd]
     * berdasarkan period: 'harian' | 'mingguan' | 'bulanan'
     */
    private function getPeriodRange(string $period): array
    {
        return match ($period) {
            'harian' => [
                Carbon::today()->startOfDay(),
                Carbon::today()->endOfDay(),
                Carbon::yesterday()->startOfDay(),
                Carbon::yesterday()->endOfDay(),
            ],
            'mingguan' => [
                Carbon::now()->startOfWeek(Carbon::MONDAY),
                Carbon::now()->endOfWeek(Carbon::SUNDAY),
                Carbon::now()->subWeek()->startOfWeek(Carbon::MONDAY),
                Carbon::now()->subWeek()->endOfWeek(Carbon::SUNDAY),
            ],
            default => [
                Carbon::now()->startOfMonth(),
                Carbon::now()->endOfMonth(),
                Carbon::now()->subMonthNoOverflow()->startOfMonth(),
                Carbon::now()->subMonthNoOverflow()->endOfMonth(),
            ],
        };
    }

    /**
     * Ekstrak nama icon dari string HTML icon (Material Symbols).
     */
    private function extractIconName(string $iconHtml): string
    {
        preg_match('/>([^<]+)</', $iconHtml, $matches);
        return trim($matches[1] ?? 'category');
    }

    /**
     * Konversi Tailwind text class → hex color untuk stroke SVG.
     */
    private function tailwindToHex(string $textClass): string
    {
        return [
            'text-purple-600'  => '#9333ea',
            'text-orange-600'  => '#ea580c',
            'text-pink-600'    => '#db2777',
            'text-sky-600'     => '#0284c7',
            'text-emerald-600' => '#059669',
            'text-yellow-600'  => '#ca8a04',
            'text-slate-600'   => '#475569',
            'text-blue-600'    => '#2563eb',
            'text-teal-600'    => '#0d9488',
            'text-violet-600'  => '#7c3aed',
            'text-amber-600'   => '#d97706',
        ][$textClass] ?? '#4b5f80';
    }

    // ──────────────────────────────────────────────────────────
    // PUBLIC METHODS
    // ──────────────────────────────────────────────────────────

    /**
     * Ringkasan perbandingan pengeluaran periode ini vs sebelumnya.
     *
     * @return array{
     *   period_label: string,
     *   current_total: float,
     *   previous_total: float,
     *   percentage_change: float,
     *   is_increase: bool,
     *   top_category: string|null,
     *   progress_value: int
     * }
     */
    public function getComparisonSummary(int $userId, string $period): array
    {
        [$currentStart, $currentEnd, $prevStart, $prevEnd] = $this->getPeriodRange($period);

        $currentExpense = (float) Expense::where('user_id', $userId)
            ->whereBetween('date', [$currentStart->toDateString(), $currentEnd->toDateString()])
            ->sum('amount');

        $prevExpense = (float) Expense::where('user_id', $userId)
            ->whereBetween('date', [$prevStart->toDateString(), $prevEnd->toDateString()])
            ->sum('amount');

        $percentageChange = 0.0;
        $isIncrease = false;

        if ($prevExpense > 0) {
            $percentageChange = (($currentExpense - $prevExpense) / $prevExpense) * 100;
            $isIncrease = $currentExpense > $prevExpense;
        } elseif ($currentExpense > 0) {
            $percentageChange = 100.0;
            $isIncrease = true;
        }

        // Kategori pengeluaran terbesar periode ini
        $topRow = Expense::where('user_id', $userId)
            ->whereBetween('date', [$currentStart->toDateString(), $currentEnd->toDateString()])
            ->select('type', DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->orderByDesc('total')
            ->first();

        $topCategoryLabel = null;
        if ($topRow) {
            $topCategoryLabel = Expense::$typeConfig[$topRow->type]['label']
                ?? ucfirst($topRow->type);
        }

        $periodLabel = match ($period) {
            'harian'   => 'Hari Ini vs Kemarin',
            'mingguan' => 'Minggu Ini vs Minggu Lalu',
            default    => 'Bulan Ini vs Bulan Lalu',
        };

        return [
            'period_label'      => $periodLabel,
            'current_total'     => $currentExpense,
            'previous_total'    => $prevExpense,
            'percentage_change' => abs(round($percentageChange, 1)),
            'is_increase'       => $isIncrease,
            'top_category'      => $topCategoryLabel,
            'progress_value'    => min(100, (int) abs(round($percentageChange))),
        ];
    }

    /**
     * Data arus kas untuk bar chart.
     * Mengembalikan heights dalam satuan persen (0–100) relatif terhadap nilai max.
     *
     * @return array{
     *   labels: string[],
     *   income: int[],
     *   expense: int[],
     *   income_raw: float[],
     *   expense_raw: float[]
     * }
     */
    public function getCashFlowChartData(int $userId, string $period): array
    {
        $labels      = [];
        $incomeRaw   = [];
        $expenseRaw  = [];

        if ($period === 'harian') {
            // Sen–Min dari minggu berjalan
            $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
            $dayNames    = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

            for ($i = 0; $i < 7; $i++) {
                $day        = $startOfWeek->copy()->addDays($i);
                $labels[]   = $dayNames[$i];
                $incomeRaw[] = (float) Income::where('user_id', $userId)
                    ->whereDate('date', $day)->sum('amount');
                $expenseRaw[] = (float) Expense::where('user_id', $userId)
                    ->whereDate('date', $day)->sum('amount');
            }
        } elseif ($period === 'mingguan') {
            // Minggu-minggu dalam bulan berjalan
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth   = Carbon::now()->endOfMonth();
            $week         = $startOfMonth->copy()->startOfWeek(Carbon::MONDAY);
            $weekNum      = 1;

            while ($week->lte($endOfMonth)) {
                $weekStart    = $week->copy()->max($startOfMonth);
                $weekEnd      = $week->copy()->endOfWeek(Carbon::SUNDAY)->min($endOfMonth);
                $labels[]     = 'M' . $weekNum;
                $incomeRaw[]  = (float) Income::where('user_id', $userId)
                    ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                    ->sum('amount');
                $expenseRaw[] = (float) Expense::where('user_id', $userId)
                    ->whereBetween('date', [$weekStart->toDateString(), $weekEnd->toDateString()])
                    ->sum('amount');
                $week->addWeek();
                $weekNum++;
            }
        } else {
            // Bulanan: ambil dari monthlyHistory (hanya bulan yang ada data)
            $history = $this->getMonthlyHistory($userId, 6);
            foreach ($history as $row) {
                $labels[]     = $row['month_name_short'];
                $incomeRaw[]  = $row['total_income'];
                $expenseRaw[] = $row['total_expense'];
            }
        }

        // Normalisasi ke 0–100% untuk tinggi bar
        $maxValue   = max(array_merge($incomeRaw, $expenseRaw, [1]));
        $incomePerc = array_map(fn ($v) => (int) round(($v / $maxValue) * 100), $incomeRaw);
        $expPerc    = array_map(fn ($v) => (int) round(($v / $maxValue) * 100), $expenseRaw);

        return [
            'labels'      => $labels,
            'income'      => $incomePerc,
            'expense'     => $expPerc,
            'income_raw'  => $incomeRaw,
            'expense_raw' => $expenseRaw,
        ];
    }

    /**
     * Breakdown pengeluaran per kategori (untuk donut chart + daftar).
     *
     * @return array<int, array{
     *   name: string,
     *   icon: string,
     *   bg_class: string,
     *   text_class: string,
     *   amount: float,
     *   percentage: float,
     *   dash_array: string,
     *   dash_offset: float,
     *   stroke_color: string
     * }>
     */
    public function getCategoryBreakdown(int $userId, string $period): array
    {
        [$currentStart, $currentEnd] = $this->getPeriodRange($period);

        $expenses = Expense::where('user_id', $userId)
            ->whereBetween('date', [$currentStart->toDateString(), $currentEnd->toDateString()])
            ->select('type', DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->orderByDesc('total')
            ->get();

        $grandTotal = $expenses->sum('total');

        if ($grandTotal <= 0) {
            return [];
        }

        $offset = 0.0;
        $result = [];

        foreach ($expenses as $exp) {
            $config     = Expense::$typeConfig[$exp->type] ?? Expense::$typeConfig['other'];
            $percentage = round(($exp->total / $grandTotal) * 100, 1);

            $result[] = [
                'name'         => $config['label'],
                'icon'         => $this->extractIconName($config['icon']),
                'bg_class'     => $config['bg'],
                'text_class'   => $config['text'],
                'amount'       => (float) $exp->total,
                'percentage'   => $percentage,
                'dash_array'   => $percentage . ', 100',
                'dash_offset'  => round(-$offset, 2),
                'stroke_color' => $this->tailwindToHex($config['text']),
            ];

            $offset += $percentage;
        }

        return $result;
    }

    /**
     * Ringkasan kartu bento: saldo wallet, tabungan, pemasukan & pengeluaran bulan ini.
     *
     * @return array{balance: float, savings: float, total_income: float, total_expense: float}
     */
    public function getSummaryCards(int $userId): array
    {
        $wallet     = Wallet::where('user_id', $userId)->first();
        $balance    = $wallet ? (float) $wallet->balance : 0.0;

        $monthStart = Carbon::now()->startOfMonth()->toDateString();
        $monthEnd   = Carbon::now()->endOfMonth()->toDateString();

        $totalIncome = (float) Income::where('user_id', $userId)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('amount');

        $totalExpense = (float) Expense::where('user_id', $userId)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->sum('amount');

        // Tabungan = total expense bertipe 'savings' sepanjang waktu
        $totalSavings = (float) Expense::where('user_id', $userId)
            ->where('type', 'savings')
            ->sum('amount');

        return [
            'balance'       => $balance,
            'savings'       => $totalSavings,
            'total_income'  => $totalIncome,
            'total_expense' => $totalExpense,
        ];
    }

    /**
     * Riwayat bulanan — HANYA bulan yang benar-benar punya data transaksi.
     * Tidak menampilkan bulan kosong.
     * Maksimum $months bulan ke belakang dari sekarang.
     *
     * @return array<int, array{
     *   month_key: string,
     *   month_name: string,
     *   month_name_short: string,
     *   total_income: float,
     *   total_expense: float,
     *   delta_pct: float,
     *   is_current: bool,
     *   income_bar: int,
     *   expense_bar: int
     * }>
     */
    public function getMonthlyHistory(int $userId, int $months = 6): array
    {
        $since = Carbon::now()->subMonths($months - 1)->startOfMonth()->toDateString();

        // Pemasukan per bulan
        $incomes = Income::where('user_id', $userId)
            ->where('date', '>=', $since)
            ->get();

        $incomeByMonth = $incomes->groupBy(function ($item) {
            return Carbon::parse($item->date)->format('Y-m');
        })->map(function ($group) {
            return $group->sum('amount');
        });

        // Pengeluaran per bulan
        $expenses = Expense::where('user_id', $userId)
            ->where('date', '>=', $since)
            ->get();

        $expenseByMonth = $expenses->groupBy(function ($item) {
            return Carbon::parse($item->date)->format('Y-m');
        })->map(function ($group) {
            return $group->sum('amount');
        });

        // Gabungkan — hanya bulan yang ada data (income ATAU expense > 0)
        $allKeys = $incomeByMonth->keys()
            ->merge($expenseByMonth->keys())
            ->unique()
            ->sort()
            ->values();

        if ($allKeys->isEmpty()) {
            return [];
        }

        $shortNames = [
            '01' => 'Jan', '02' => 'Feb', '03' => 'Mar',
            '04' => 'Apr', '05' => 'Mei', '06' => 'Jun',
            '07' => 'Jul', '08' => 'Agu', '09' => 'Sep',
            '10' => 'Okt', '11' => 'Nov', '12' => 'Des',
        ];
        $currentKey = Carbon::now()->format('Y-m');

        // Hitung max untuk bar chart proporsional
        $maxValue = 1.0;
        foreach ($allKeys as $key) {
            $maxValue = max($maxValue, (float) ($incomeByMonth[$key] ?? 0), (float) ($expenseByMonth[$key] ?? 0));
        }

        $result     = [];
        $prevExpense = null;

        foreach ($allKeys as $monthKey) {
            $income  = (float) ($incomeByMonth[$monthKey] ?? 0);
            $expense = (float) ($expenseByMonth[$monthKey] ?? 0);

            [$year, $month] = explode('-', $monthKey);
            $short    = $shortNames[$month] ?? $month;
            $fullName = $short . ' ' . $year;

            $deltaPct = 0.0;
            if ($prevExpense !== null && $prevExpense > 0) {
                $deltaPct = round((($expense - $prevExpense) / $prevExpense) * 100, 1);
            }

            $result[] = [
                'month_key'       => $monthKey,
                'month_name'      => $fullName,
                'month_name_short' => $short,
                'total_income'    => $income,
                'total_expense'   => $expense,
                'delta_pct'       => $deltaPct,
                'is_current'      => $monthKey === $currentKey,
                'income_bar'      => (int) round(($income / $maxValue) * 100),
                'expense_bar'     => (int) round(($expense / $maxValue) * 100),
            ];

            $prevExpense = $expense;
        }

        return $result;
    }
}
