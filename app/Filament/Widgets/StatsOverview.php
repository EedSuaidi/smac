<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\WalletBalance;
use App\Models\Transaction;
use App\Models\Report;
use App\Models\Currency;
use App\Services\CryptoPriceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = '300s';

    public static function canView(): bool
    {
        return auth()->user()->role === 'user';
    }

    protected function getStats(): array
    {
        $userId = Auth::id();

        if (!$userId) {
            return [];
        }

        $cryptoPriceService = app(CryptoPriceService::class);
        $usdCurrency = Cache::remember('usd_currency_id_for_stats', 3600, function () {
            return Currency::where('symbol', 'USD')->first();
        });

        if (!$usdCurrency) {
            Log::error('USD currency not found for dashboard stats calculation.');
            return [];
        }

        // --- 1. Total Portfolio Value (dihitung live dan di-cache untuk 60 detik) ---
        $totalPortfolioValue = Cache::remember("user_{$userId}_total_portfolio_value", 60, function () use ($userId, $cryptoPriceService, $usdCurrency) {
            $value = 0;
            $walletBalances = WalletBalance::where('user_id', $userId)->with('currency')->get();

            foreach ($walletBalances as $balance) {
                $currency = $balance->currency;
                $amount = (float) $balance->balance;

                if ($currency->currency_type === 'fiat' && $currency->symbol === 'USD') {
                    $value += $amount;
                } elseif ($currency->currency_type === 'crypto') {
                    $price = (float) $cryptoPriceService->getPrice($currency->symbol, 'USD');
                    $value += $amount * $price;
                }
            }
            return $value;
        });

        // --- 2. Hitung Live Asset Growth & Percentage (dibandingkan dengan laporan terakhir) ---
        $latestReport = Report::where('user_id', $userId)
            ->orderBy('report_date', 'desc')
            ->first();

        // Inisialisasi nilai
        $displayGrowth = 0.00;
        $displayGrowthPercentage = 0.00;
        $growthColor = 'gray';
        $growthIcon = 'heroicon-m-arrows-right-left'; // Ikon default

        if ($latestReport) {
            // Ambil nilai portofolio dari laporan terakhir (misalnya kemarin jam 22:00)
            $previousPortfolioValue = (float) $latestReport->daily_portfolio_value;

            // Hitung selisih antara nilai live dan nilai laporan terakhir
            $displayGrowth = $totalPortfolioValue - $previousPortfolioValue;

            // Hitung persentase pertumbuhan
            if ($previousPortfolioValue != 0) {
                $displayGrowthPercentage = ($displayGrowth / $previousPortfolioValue) * 100;
            } else if ($totalPortfolioValue != 0) {
                // Jika nilai kemarin 0 tapi hari ini tidak 0, pertumbuhan sangat besar
                $displayGrowthPercentage = 9999.99; // Anda bisa atur batas atau tampilkan 'Inf'
            } else {
                $displayGrowthPercentage = 0.00; // Keduanya 0
            }

            // Tentukan warna dan ikon berdasarkan persentase pertumbuhan
            if ($displayGrowthPercentage > 0) {
                $growthColor = 'success';
                $growthIcon = 'heroicon-m-arrow-trending-up';
            } elseif ($displayGrowthPercentage < 0) {
                $growthColor = 'danger';
                $growthIcon = 'heroicon-m-arrow-trending-down';
            }
            // Untuk 0% pertumbuhan, warna dan ikon default sudah sesuai
        }

        // --- 3. Total Deposit (sudah benar) ---
        $totalDeposit = Cache::remember("user_{$userId}_total_deposit", 3600, function () use ($userId, $usdCurrency) {
            return (float) Transaction::where('user_id', $userId)
                ->where('transaction_type', 'deposit')
                ->where('currency_id', $usdCurrency->id)
                ->sum('amount');
        });

        // --- 4. Total Withdraw (sudah benar) ---
        $totalWithdraw = Cache::remember("user_{$userId}_total_withdraw", 3600, function () use ($userId, $usdCurrency) {
            return (float) Transaction::where('user_id', $userId)
                ->where('transaction_type', 'withdraw')
                ->where('currency_id', $usdCurrency->id)
                ->sum('amount');
        });

        return [
            Stat::make('Total Portfolio Value', '$' . number_format($totalPortfolioValue, 2))
                ->description(
                    // Format deskripsi dengan tanda + atau - dan 2 desimal
                    ($displayGrowth >= 0 ? '$' : '-$') . number_format(abs($displayGrowth), 2) . ' (' .
                        ($displayGrowthPercentage >= 0 ? '+' : '') . number_format($displayGrowthPercentage, 2) . '%)'
                )
                ->descriptionIcon($growthIcon)
                ->color($growthColor),

            Stat::make('Total Deposits', '$' . number_format($totalDeposit, 2))
                ->description('All-time fiat deposits')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Total Withdrawals', '$' . number_format($totalWithdraw, 2))
                ->description('All-time fiat withdrawals')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('danger'),
        ];
    }
}
