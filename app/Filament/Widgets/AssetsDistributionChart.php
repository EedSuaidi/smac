<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\WalletBalance;
use Illuminate\Support\Facades\Auth;
use App\Services\CryptoPriceService;

class AssetsDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Assets Distribution';

    protected static ?int $sort = 3; // Atur urutan widget

    protected function getType(): string
    {
        return 'pie'; // Jenis grafik: pie
    }

    public static function canView(): bool
    {
        // Hanya user dengan role 'user' yang dapat melihat widget ini
        return auth()->user()->role === 'user';
    }

    protected function getData(): array
    {
        // Ambil data currency yang dimiliki user
        $userId = Auth::id();
        $cryptoPriceService = app(CryptoPriceService::class);

        $currencies = WalletBalance::query()
            ->where('user_id', $userId)
            ->with('currency')
            ->get()
            ->mapWithKeys(function ($walletBalance) use ($cryptoPriceService) {
                $currencySymbol = strtoupper($walletBalance->currency->symbol); // Gunakan simbol dalam huruf besar
                $price = $cryptoPriceService->getPrice($currencySymbol); // Ambil harga dari service
                $totalAsset = round($walletBalance->balance * $price); // Hitung total asset
                return [$currencySymbol => $totalAsset];
            });

        $groupedCurrencies = [
            'USD' => $currencies->get('USD', 0),
            'BTC' => $currencies->get('BTC', 0),
            'Altcoin' => $currencies->except(['USD', 'BTC'])->sum(),
        ];

        // Format data untuk chart
        $labels = array_keys($groupedCurrencies);
        $values = array_values($groupedCurrencies); // Total asset per kategori

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Assets Distribution',
                    'data' => $values,
                    'backgroundColor' => [
                        'rgba(76, 175, 80, 0.2)',
                        'rgba(255, 152, 0, 0.2)',
                        'rgba(33, 150, 243, 0.2)',
                    ], // Gunakan warna dinamis
                    'borderColor' => [
                        '#4CAF50',
                        '#FF9800',
                        '#2196F3',
                    ],
                    'borderWidth' => 1,
                ],
            ],
        ];
    }


    protected function getOptions(): array
    {
        return [
            'maintainAspectRatio' => false, // Nonaktifkan rasio aspek default
            'responsive' => true, // Pastikan chart tetap responsif
            'plugins' => [
                'legend' => [
                    'position' => 'bottom', // Atur posisi legenda
                ],
            ],
            'layout' => [
                'padding' => 10, // Tambahkan padding jika diperlukan
            ],
            'scales' => [
                'x' => [
                    'display' => false, // Nonaktifkan sumbu X
                ],
                'y' => [
                    'display' => false, // Nonaktifkan sumbu Y
                ],
            ],
        ];
    }
}
