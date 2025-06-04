<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Report;
use App\Models\Currency;
use App\Services\CryptoPriceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateDailyReports extends Command
{
    /**
     * Nama dan tanda tangan perintah konsol.
     * Inilah yang akan Anda ketik di terminal: php artisan report:generate-daily
     *
     * @var string
     */
    protected $signature = 'report:generate-daily';

    /**
     * Deskripsi perintah konsol.
     *
     * @var string
     */
    protected $description = 'Generates daily portfolio reports for all users.';

    protected CryptoPriceService $cryptoPriceService; // Properti untuk menyimpan instance service

    /**
     * Buat instance perintah baru.
     * Dependensi (CryptoPriceService) akan diinjeksikan secara otomatis oleh Laravel.
     *
     * @return void
     */
    public function __construct(CryptoPriceService $cryptoPriceService)
    {
        parent::__construct();
        $this->cryptoPriceService = $cryptoPriceService;
    }

    /**
     * Jalankan perintah konsol.
     * Ini adalah metode utama yang akan dieksekusi saat perintah dijalankan.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->info('Starting daily report generation...');

        $reportDate = Carbon::today();
        $yesterday = $reportDate->copy()->subDay();

        $usdCurrency = Currency::where('symbol', 'USD')->first();
        if (!$usdCurrency) {
            $this->error('USD currency not found in database. Please ensure it\'s seeded.');
            Log::error('GenerateDailyReports: USD currency not found, cannot generate reports.');
            return;
        }

        $users = User::all();

        foreach ($users as $user) {
            DB::transaction(function () use ($user, $reportDate, $yesterday, $usdCurrency) {
                try {
                    $currentPortfolioValue = 0;

                    $walletBalances = $user->walletBalances()->with('currency')->get();

                    foreach ($walletBalances as $balance) {
                        $currency = $balance->currency;
                        $amount = (float) $balance->balance;

                        if ($currency->currency_type === 'fiat' && $currency->symbol === 'USD') {
                            $currentPortfolioValue += $amount;
                        } elseif ($currency->currency_type === 'crypto') {
                            $price = (float) $this->cryptoPriceService->getPrice($currency->symbol, 'USD');
                            $currentPortfolioValue += $amount * $price;
                        }
                    }

                    $previousReport = Report::where('user_id', $user->id)
                        ->whereDate('report_date', $yesterday)
                        ->first();

                    $dailyAssetGrowth = 0.00;
                    $dailyAssetGrowthPercentage = 0.00;

                    if ($previousReport) {
                        $previousPortfolioValue = (float) $previousReport->daily_portfolio_value;
                        $dailyAssetGrowth = $currentPortfolioValue - $previousPortfolioValue;

                        if ($previousPortfolioValue != 0) {
                            $dailyAssetGrowthPercentage = ($dailyAssetGrowth / $previousPortfolioValue) * 100;
                        } else if ($currentPortfolioValue != 0) {
                            $dailyAssetGrowthPercentage = 9999.99; // Represent as a very large number
                        } else {
                            $dailyAssetGrowthPercentage = 0.00;
                        }
                    }

                    Report::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'report_date' => $reportDate,
                        ],
                        [
                            'daily_portfolio_value' => (string) number_format($currentPortfolioValue, 8, '.', ''),
                            'daily_asset_growth' => (string) number_format($dailyAssetGrowth, 8, '.', ''),
                            'daily_asset_growth_percentage' => (string) number_format($dailyAssetGrowthPercentage, 2, '.', ''), // <-- TAMBAHKAN BARIS INI
                        ]
                    );

                    $this->info("Report generated for User ID: {$user->id} on {$reportDate->toDateString()}. Value: $" . number_format($currentPortfolioValue, 2) . ", Growth: " . number_format($dailyAssetGrowthPercentage, 2) . "%");
                } catch (\Exception $e) {
                    $this->error("Error generating report for User ID: {$user->id} on {$reportDate->toDateString()}. Message: " . $e->getMessage());
                    Log::error("GenerateDailyReports: Error for User ID: {$user->id} on {$reportDate->toDateString()}.", [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'user_id' => $user->id,
                        'report_date' => $reportDate->toDateString(),
                    ]);
                    throw $e;
                }
            });
        }

        $this->info('Daily report generation completed.');
    }
}
