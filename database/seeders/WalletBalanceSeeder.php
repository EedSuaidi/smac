<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WalletBalanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $walletBalances = [
            [
                'user_id' => 2, // Eed
                'currency_id' => 1, // USD
                'balance' => 3551,
            ],
            [
                'user_id' => 2,
                'currency_id' => 2, // BTC
                'balance' => 0.145, // 0.175 - 0.03 (sell)
            ],
            [
                'user_id' => 2,
                'currency_id' => 3, // ETH
                'balance' => 2,
            ],
            [
                'user_id' => 2,
                'currency_id' => 5, // BNB
                'balance' => 8.35,
            ],
            [
                'user_id' => 2,
                'currency_id' => 6, // SOL
                'balance' => 28.95,
            ],
        ];

        foreach ($walletBalances as $balance) {
            \App\Models\WalletBalance::updateOrCreate(
                [
                    'user_id' => $balance['user_id'],
                    'currency_id' => $balance['currency_id'],
                ],
                [
                    'balance' => (string) number_format($balance['balance'], 8, '.', ''),
                ]
            );
        }
    }
}
