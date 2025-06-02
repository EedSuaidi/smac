<?php

namespace Database\Seeders;

use App\Models\Currency;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Currency::updateOrCreate(
            ['symbol' => 'USD'], // Kriteria pencarian
            [
                'name' => 'US Dollar',
                'currency_type' => 'fiat', // Pastikan ini sesuai dengan nama kolom di DB Anda
            ]
        );

        $cryptocurrencies = [
            ['name' => 'Bitcoin', 'symbol' => 'BTC', 'currency_type' => 'crypto'],
            ['name' => 'Ethereum', 'symbol' => 'ETH', 'currency_type' => 'crypto'],
            ['name' => 'Tether', 'symbol' => 'USDT', 'currency_type' => 'crypto'],
            ['name' => 'BNB', 'symbol' => 'BNB', 'currency_type' => 'crypto'],
            ['name' => 'Solana', 'symbol' => 'SOL', 'currency_type' => 'crypto'],
            ['name' => 'USD Coin', 'symbol' => 'USDC', 'currency_type' => 'crypto'],
            ['name' => 'XRP', 'symbol' => 'XRP', 'currency_type' => 'crypto'],
            ['name' => 'Dogecoin', 'symbol' => 'DOGE', 'currency_type' => 'crypto'],
            ['name' => 'Cardano', 'symbol' => 'ADA', 'currency_type' => 'crypto'],
            ['name' => 'Avalanche', 'symbol' => 'AVAX', 'currency_type' => 'crypto'],
            ['name' => 'Shiba Inu', 'symbol' => 'SHIB', 'currency_type' => 'crypto'],
            ['name' => 'Polkadot', 'symbol' => 'DOT', 'currency_type' => 'crypto'],
            ['name' => 'Chainlink', 'symbol' => 'LINK', 'currency_type' => 'crypto'],
            ['name' => 'Litecoin', 'symbol' => 'LTC', 'currency_type' => 'crypto'],
            ['name' => 'Polygon', 'symbol' => 'MATIC', 'currency_type' => 'crypto'],
            ['name' => 'Wrapped Bitcoin', 'symbol' => 'WBTC', 'currency_type' => 'crypto'],
            ['name' => 'Tron', 'symbol' => 'TRX', 'currency_type' => 'crypto'],
            ['name' => 'Near Protocol', 'symbol' => 'NEAR', 'currency_type' => 'crypto'],
            ['name' => 'Bitcoin Cash', 'symbol' => 'BCH', 'currency_type' => 'crypto'],
            ['name' => 'Dai', 'symbol' => 'DAI', 'currency_type' => 'crypto'],
            ['name' => 'Uniswap', 'symbol' => 'UNI', 'currency_type' => 'crypto'],
            ['name' => 'Kaspa', 'symbol' => 'KAS', 'currency_type' => 'crypto'],
            ['name' => 'Immutable', 'symbol' => 'IMX', 'currency_type' => 'crypto'],
            ['name' => 'Ethereum Classic', 'symbol' => 'ETC', 'currency_type' => 'crypto'],
            ['name' => 'Monero', 'symbol' => 'XMR', 'currency_type' => 'crypto'],
            ['name' => 'Render', 'symbol' => 'RNDR', 'currency_type' => 'crypto'],
            ['name' => 'Aptos', 'symbol' => 'APT', 'currency_type' => 'crypto'],
            ['name' => 'Cosmos', 'symbol' => 'ATOM', 'currency_type' => 'crypto'],
            ['name' => 'Filecoin', 'symbol' => 'FIL', 'currency_type' => 'crypto'],
            ['name' => 'Internet Computer', 'symbol' => 'ICP', 'currency_type' => 'crypto'],
            ['name' => 'Injective', 'symbol' => 'INJ', 'currency_type' => 'crypto'],
            ['name' => 'OKB', 'symbol' => 'OKB', 'currency_type' => 'crypto'],
            ['name' => 'Arbitrum', 'symbol' => 'ARB', 'currency_type' => 'crypto'],
            ['name' => 'VeChain', 'symbol' => 'VET', 'currency_type' => 'crypto'],
            ['name' => 'Mantle', 'symbol' => 'MNT', 'currency_type' => 'crypto'],
            ['name' => 'Theta Network', 'symbol' => 'THETA', 'currency_type' => 'crypto'],
            ['name' => 'First Digital USD', 'symbol' => 'FDUSD', 'currency_type' => 'crypto'],
            ['name' => 'Stacks', 'symbol' => 'STX', 'currency_type' => 'crypto'],
            ['name' => 'Hedera', 'symbol' => 'HBAR', 'currency_type' => 'crypto'],
            ['name' => 'The Graph', 'symbol' => 'GRT', 'currency_type' => 'crypto'],
            ['name' => 'Celestia', 'symbol' => 'TIA', 'currency_type' => 'crypto'],
            ['name' => 'Optimism', 'symbol' => 'OP', 'currency_type' => 'crypto'],
            ['name' => 'Fantom', 'symbol' => 'FTM', 'currency_type' => 'crypto'],
            ['name' => 'Algorand', 'symbol' => 'ALGO', 'currency_type' => 'crypto'],
            ['name' => 'SingularityNET', 'symbol' => 'AGIX', 'currency_type' => 'crypto'],
            ['name' => 'Decentraland', 'symbol' => 'MANA', 'currency_type' => 'crypto'],
            ['name' => 'Theta Fuel', 'symbol' => 'TFUEL', 'currency_type' => 'crypto'],
            ['name' => 'Axie Infinity', 'symbol' => 'AXS', 'currency_type' => 'crypto'],
            ['name' => 'eCash', 'symbol' => 'XEC', 'currency_type' => 'crypto'],
            ['name' => 'Flow', 'symbol' => 'FLOW', 'currency_type' => 'crypto'],
        ];

        foreach ($cryptocurrencies as $currencyData) {
            Currency::updateOrCreate(
                ['symbol' => $currencyData['symbol']], // Kriteria pencarian
                [
                    'name' => $currencyData['name'],
                    'currency_type' => $currencyData['currency_type'], // Pastikan ini ditambahkan
                ]
            );
        }
    }
}
