<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Carbon\Carbon;

class StatisticController extends Controller
{
    public function index()
    {
        return view('user.statistic.index');
    }

    public function show(int $year, int $month)
    {
        // Validasi bulan dan tahun
        if ($month < 1 || $month > 12 || $year < 2000 || $year > now()->year) {
            abort(404);
        }

        $date = Carbon::create($year, $month, 1);
        $monthName = $date->translatedFormat('F Y');

        return view('user.statistic.show', compact('year', 'month', 'monthName'));
    }
}
