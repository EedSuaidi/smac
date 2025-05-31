<?php

namespace Database\Seeders;

use App\Models\Crypto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CryptoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cryptocurrencies = [
            ['name' => 'Bitcoin', 'symbol' => 'BTC'],
            ['name' => 'Ethereum', 'symbol' => 'ETH'],
            ['name' => 'Tether', 'symbol' => 'USDT'],
            ['name' => 'BNB', 'symbol' => 'BNB'],
            ['name' => 'Solana', 'symbol' => 'SOL'],
            ['name' => 'USD Coin', 'symbol' => 'USDC'],
            ['name' => 'XRP', 'symbol' => 'XRP'],
            ['name' => 'Dogecoin', 'symbol' => 'DOGE'],
            ['name' => 'Cardano', 'symbol' => 'ADA'],
            ['name' => 'Avalanche', 'symbol' => 'AVAX'],
            ['name' => 'Shiba Inu', 'symbol' => 'SHIB'],
            ['name' => 'Polkadot', 'symbol' => 'DOT'],
            ['name' => 'Chainlink', 'symbol' => 'LINK'],
            ['name' => 'Litecoin', 'symbol' => 'LTC'],
            ['name' => 'Polygon', 'symbol' => 'MATIC'],
            ['name' => 'Wrapped Bitcoin', 'symbol' => 'WBTC'],
            ['name' => 'Tron', 'symbol' => 'TRX'],
            ['name' => 'Near Protocol', 'symbol' => 'NEAR'],
            ['name' => 'Bitcoin Cash', 'symbol' => 'BCH'],
            ['name' => 'Dai', 'symbol' => 'DAI'],
            ['name' => 'Uniswap', 'symbol' => 'UNI'],
            ['name' => 'Kaspa', 'symbol' => 'KAS'],
            ['name' => 'Immutable', 'symbol' => 'IMX'],
            ['name' => 'Ethereum Classic', 'symbol' => 'ETC'],
            ['name' => 'Monero', 'symbol' => 'XMR'],
            ['name' => 'Render', 'symbol' => 'RNDR'],
            ['name' => 'Aptos', 'symbol' => 'APT'],
            ['name' => 'Cosmos', 'symbol' => 'ATOM'],
            ['name' => 'Filecoin', 'symbol' => 'FIL'],
            ['name' => 'Internet Computer', 'symbol' => 'ICP'],
            ['name' => 'Injective', 'symbol' => 'INJ'],
            ['name' => 'OKB', 'symbol' => 'OKB'],
            ['name' => 'Arbitrum', 'symbol' => 'ARB'],
            ['name' => 'VeChain', 'symbol' => 'VET'],
            ['name' => 'Mantle', 'symbol' => 'MNT'],
            ['name' => 'Theta Network', 'symbol' => 'THETA'],
            ['name' => 'First Digital USD', 'symbol' => 'FDUSD'],
            ['name' => 'Stacks', 'symbol' => 'STX'],
            ['name' => 'Hedera', 'symbol' => 'HBAR'],
            ['name' => 'The Graph', 'symbol' => 'GRT'],
            ['name' => 'Celestia', 'symbol' => 'TIA'],
            ['name' => 'Optimism', 'symbol' => 'OP'],
            ['name' => 'Fantom', 'symbol' => 'FTM'],
            ['name' => 'Algorand', 'symbol' => 'ALGO'],
            ['name' => 'SingularityNET', 'symbol' => 'AGIX'],
            ['name' => 'Decentraland', 'symbol' => 'MANA'],
            ['name' => 'Theta Fuel', 'symbol' => 'TFUEL'],
            ['name' => 'Axie Infinity', 'symbol' => 'AXS'],
            ['name' => 'eCash', 'symbol' => 'XEC'],
            ['name' => 'Flow', 'symbol' => 'FLOW'],
        ];

        foreach ($cryptocurrencies as $cryptoData) {
            // Menggunakan updateOrCreate untuk menghindari duplikasi
            // Jika 'symbol' sudah ada, akan diupdate. Jika tidak, akan dibuat baru.
            Crypto::updateOrCreate(
                ['symbol' => $cryptoData['symbol']], // Kriteria pencarian
                ['name' => $cryptoData['name']]      // Data yang akan diupdate/dibuat
            );
        }
    }
}
