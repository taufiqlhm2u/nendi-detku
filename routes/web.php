<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\BerandaController;
use App\Http\Controllers\User\ExpenseController;
use App\Http\Controllers\User\HistoryController;
use App\Http\Controllers\User\IncomeController;
use App\Http\Controllers\User\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.verif');

    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.verif');

    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/beranda', [BerandaController::class, 'index'])->name('beranda');

    Route::resource('pemasukan', IncomeController::class)->names('incomes');

    Route::resource('pengeluaran', ExpenseController::class)->names('expenses');

    Route::get('/riwayat', [HistoryController::class, 'index'])->name('history');

    Route::resource('profil', ProfileController::class)->only(['index', 'edit', 'update', 'destroy'])->names('profile');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});
