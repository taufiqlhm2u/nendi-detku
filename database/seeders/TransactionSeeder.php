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
                'type'       => 'Salary',
                'amount'     => 8_000_000,
                'note'       => 'Gaji bulan Juni',
                'created_at' => Carbon::now()->subDays(10)->setTime(8, 0),
            ],
            [
                'group'      => 'income',
                'type'       => 'Freelance',
                'amount'     => 1_500_000,
                'note'       => 'Proyek desain website',
                'created_at' => Carbon::now()->subDays(9)->setTime(11, 30),
            ],

            // ── Kelompok 2: 5 Pengeluaran ─────────────────────
            [
                'group'      => 'expense',
                'type'       => 'Bills',
                'amount'     => 150_000,
                'note'       => 'Tagihan internet bulanan',
                'created_at' => Carbon::now()->subDays(8)->setTime(9, 0),
            ],
            [
                'group'      => 'expense',
                'type'       => 'Transportation',
                'amount'     => 50_000,
                'note'       => 'Grab ke kantor',
                'created_at' => Carbon::now()->subDays(7)->setTime(7, 45),
            ],
            [
                'group'      => 'expense',
                'type'       => 'Personal Needs',
                'amount'     => 120_000,
                'note'       => 'Beli sabun & shampoo',
                'created_at' => Carbon::now()->subDays(6)->setTime(16, 20),
            ],
            [
                'group'      => 'expense',
                'type'       => 'Shopping',
                'amount'     => 250_000,
                'note'       => 'Belanja mingguan supermarket',
                'created_at' => Carbon::now()->subDays(5)->setTime(14, 0),
            ],
            [
                'group'      => 'expense',
                'type'       => 'Snacks',
                'amount'     => 35_000,
                'note'       => 'Kopi & cemilan sore',
                'created_at' => Carbon::now()->subDays(4)->setTime(15, 30),
            ],

            // ── Kelompok 3: 1 Pemasukan ───────────────────────
            [
                'group'      => 'income',
                'type'       => 'Bonus',
                'amount'     => 500_000,
                'note'       => 'Bonus performa Q2',
                'created_at' => Carbon::now()->subDays(3)->setTime(10, 0),
            ],

            // ── Kelompok 4: 3 Pengeluaran ─────────────────────
            [
                'group'      => 'expense',
                'type'       => 'Bills',
                'amount'     => 320_000,
                'note'       => 'Tagihan listrik PLN',
                'created_at' => Carbon::now()->subDays(2)->setTime(9, 15),
            ],
            [
                'group'      => 'expense',
                'type'       => 'Transportation',
                'amount'     => 400_000,
                'note'       => 'Bensin mobil minggu ini',
                'created_at' => Carbon::now()->subDays(1)->setTime(7, 30),
            ],
            [
                'group'      => 'expense',
                'type'       => 'Snacks',
                'amount'     => 45_000,
                'note'       => 'Makan siang kantin',
                'created_at' => Carbon::now()->subHours(2),
            ],
        ];

        // ── Buat transaksi & update saldo wallet ──────────────
        foreach ($transactions as $data) {
            $timestamp = $data['created_at'];

            if ($data['group'] === 'income') {
                Income::create([
                    'user_id'    => $user->id,
                    'type'       => $data['type'],
                    'amount'     => $data['amount'],
                    'note'       => $data['note'],
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
