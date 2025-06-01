<?php

// namespace App\Services;

// use Illuminate\Support\Facades\Http;

// class CoinGeckoService
// {
//     protected $baseUrl = 'https://api.coingecko.com/api/v3';

//     public function getPrice(string $symbol): float
//     {
//         $response = Http::get("{$this->baseUrl}/simple/price", [
//             'ids' => $symbol,
//             'vs_currencies' => 'usd',
//         ]);

//         if ($response->successful()) {
//             return $response->json()[$symbol]['usd'] ?? 0;
//         }

//         return 0; // Default jika gagal
//     }
// }
