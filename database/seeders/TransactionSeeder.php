<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transactions = [
            [
                'user_id' => 2, // Eed
                'currency_id' => 1, // USD
                'amount' => 30000,
                'price' => 1,
                'total' => 30000,
                'transaction_type' => 'deposit',
                'transaction_date' => '2025-05-27',
            ],
            [
                'user_id' => 2,
                'currency_id' => 2, // BTC
                'amount' => 0.175,
                'price' => 90000,
                'total' => 15750,
                'transaction_type' => 'buy',
                'transaction_date' => '2025-05-28',
            ],
            [
                'user_id' => 2,
                'currency_id' => 3, // ETH
                'amount' => 2,
                'price' => 2100,
                'total' => 4200,
                'transaction_type' => 'buy',
                'transaction_date' => '2025-05-28',
            ],
            [
                'user_id' => 2,
                'currency_id' => 2, // BTC
                'amount' => 0.03,
                'price' => 105000,
                'total' => 3150,
                'transaction_type' => 'sell',
                'transaction_date' => '2025-05-30',
            ],
            [
                'user_id' => 2,
                'currency_id' => 5, // BNB
                'amount' => 8.35,
                'price' => 500,
                'total' => 4175,
                'transaction_type' => 'buy',
                'transaction_date' => '2025-05-31',
            ],
            [
                'user_id' => 2,
                'currency_id' => 6, // SOL
                'amount' => 28.95,
                'price' => 120,
                'total' => 3474,
                'transaction_type' => 'buy',
                'transaction_date' => '2025-06-01',
            ],
            [
                'user_id' => 2, // Eed
                'currency_id' => 1, // USD
                'amount' => 2000,
                'price' => 1,
                'total' => 2000,
                'transaction_type' => 'withdraw',
                'transaction_date' => '2025-06-02',
            ],
        ];

        foreach ($transactions as $transaction) {
            \App\Models\Transaction::updateOrCreate(
                [
                    'user_id' => $transaction['user_id'],
                    'currency_id' => $transaction['currency_id'],
                    'transaction_date' => $transaction['transaction_date'],
                ],
                [
                    'amount' => $transaction['amount'],
                    'price' => $transaction['price'],
                    'total' => $transaction['total'],
                    'transaction_type' => $transaction['transaction_type'],
                ]
            );
        }
    }
}
