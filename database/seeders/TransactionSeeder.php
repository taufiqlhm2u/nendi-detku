<?php

namespace Database\Seeders;

use App\Models\Expense;
use App\Models\Income;
use App\Models\User;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil user biasa (bukan admin)
        $user = User::where('email', 'pass@mail.com')->first();

        if (! $user) {
            return;
        }

        // ── Buat wallet kosong untuk user ──────────────────
        $wallet = Wallet::create([
            'user_id' => $user->id,
            'balance' => 0,
        ]);

        // ══════════════════════════════════════════════════════
        // Urutan selang-seling:
        //   2 income  → 5 expense  → 1 income  → 3 expense
        // Total: 3 income, 8 expense
        //
        // Timestamp dari paling lama (index 0) ke paling baru
        // sehingga created_at DESC di dashboard tampil rapi.
        // ══════════════════════════════════════════════════════

        $transactions = [

            // ── Kelompok 1: 2 Pemasukan ───────────────────────
            [
                'group'      => 'income',
                'type'       => 'salary',
                'amount'     => 8_000_000,
                'note'       => 'Gaji bulan Juni',
                'date' => Carbon::now()->subDays(40)->setTime(8, 0),
            ],
            [
                'group'      => 'income',
                'type'       => 'freelance',
                'amount'     => 1_500_000,
                'note'       => 'Proyek desain website',
                'date' => Carbon::now()->subDays(38)->setTime(11, 30),
            ],

            // ── Kelompok 2: 5 Pengeluaran ─────────────────────
            [
                'group'      => 'expense',
                'type'       => 'bills',
                'amount'     => 150_000,
                'note'       => 'Tagihan internet bulanan',
                'date' => Carbon::now()->subDays(36)->setTime(9, 0),
            ],
            [
                'group'      => 'expense',
                'type'       => 'transportation',
                'amount'     => 50_000,
                'note'       => 'Grab ke kantor',
                'date' => Carbon::now()->subDays(35)->setTime(7, 45),
            ],
            [
                'group'      => 'expense',
                'type'       => 'personal needs',
                'amount'     => 120_000,
                'note'       => 'Beli sabun & shampoo',
                'date' => Carbon::now()->subDays(6)->setTime(16, 20),
            ],
            [
                'group'      => 'expense',
                'type'       => 'shopping',
                'amount'     => 250_000,
                'note'       => 'Belanja mingguan supermarket',
                'date' => Carbon::now()->subDays(5)->setTime(14, 0),
            ],
            [
                'group'      => 'expense',
                'type'       => 'snacks',
                'amount'     => 35_000,
                'note'       => 'Kopi & cemilan sore',
                'date' => Carbon::now()->subDays(4)->setTime(15, 30),
            ],

            // ── Kelompok 3: 1 Pemasukan ───────────────────────
            [
                'group'      => 'income',
                'type'       => 'bonus',
                'amount'     => 500_000,
                'note'       => 'Bonus performa Q2',
                'date' => Carbon::now()->subDays(3)->setTime(10, 0),
            ],

            // ── Kelompok 4: 3 Pengeluaran ─────────────────────
            [
                'group'      => 'expense',
                'type'       => 'bills',
                'amount'     => 320_000,
                'note'       => 'Tagihan listrik PLN',
                'date' => Carbon::now()->subDays(2)->setTime(9, 15),
            ],
            [
                'group'      => 'expense',
                'type'       => 'transportation',
                'amount'     => 400_000,
                'note'       => 'Bensin mobil minggu ini',
                'date' => Carbon::now()->subDays(1)->setTime(7, 30),
            ],
            [
                'group'      => 'expense',
                'type'       => 'snacks',
                'amount'     => 45_000,
                'note'       => 'Makan siang kantin',
                'date' => Carbon::now()->subHours(2),
            ],
        ];

        // ── Buat transaksi & update saldo wallet ──────────────
        foreach ($transactions as $data) {
            $timestamp = $data['date'];

            if ($data['group'] === 'income') {
                Income::create([
                    'user_id'    => $user->id,
                    'type'       => $data['type'],
                    'amount'     => $data['amount'],
                    'note'       => $data['note'],
                    'date'       => $data['date'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                // Tambah saldo wallet
                $wallet->credit($data['amount']);

            } else {
                Expense::create([
                    'user_id'    => $user->id,
                    'type'       => $data['type'],
                    'amount'     => $data['amount'],
                    'note'       => $data['note'],
                    'date'       => $data['date'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);

                // Kurangi saldo wallet
                $wallet->debit($data['amount']);
            }
        }

        // Tampilkan saldo akhir di output seeder
        $wallet->refresh();
        $this->command->info(
            "Wallet user [{$user->email}] → Rp " .
            number_format($wallet->balance, 0, ',', '.')
        );
    }
}
