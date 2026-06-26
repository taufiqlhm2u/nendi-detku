<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class StatisticController extends Controller
{
    public function index()
    {
        return view('user.statistic');
    }
}
