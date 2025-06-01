<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CryptoPriceService
{
    protected $baseUrl = 'https://min-api.cryptocompare.com/data'; // Base URL CryptoCompare
    protected $cacheTtl = 300; // Cache selama 5 menit (300 detik)

    public function getPrice(string $symbol, string $currency = 'USD'): float
    {
        // Pastikan symbol tidak kosong
        if (empty($symbol)) {
            Log::warning('CryptoPriceService: Attempted to get price for empty symbol.');
            return 0;
        }

        $cacheKey = "cryptocompare_price_{$symbol}_{$currency}";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::get("{$this->baseUrl}/price", [
                'fsym' => strtoupper($symbol), // Simbol kripto (F-rom SY-mbol)
                'tsyms' => strtoupper($currency), // Mata uang tujuan (T-o SY-mbols)
            ]);

            Log::info('CryptoCompare API Response:', [
                'symbol' => $symbol,
                'currency' => $currency,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            if ($response->successful()) {
                $price = $response->json()[strtoupper($currency)] ?? 0;
                Cache::put($cacheKey, $price, $this->cacheTtl);
                return (float) $price; // Pastikan dikembalikan sebagai float
            } else {
                Log::error('CryptoCompare API Error:', [
                    'symbol' => $symbol,
                    'currency' => $currency,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('CryptoPriceService Exception:', [
                'message' => $e->getMessage(),
                'symbol' => $symbol,
            ]);
        }

        return 0;
    }
}
