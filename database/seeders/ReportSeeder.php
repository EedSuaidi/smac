<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reports = [
            [
                'user_id' => 2,
                'daily_portfolio_value' => 30000,
                'daily_asset_growth' => 300, // 1.0% dari 30000
                'daily_asset_growth_percentage' => 1.0,
                'report_date' => '2025-06-03',
            ],
            [
                'user_id' => 2,
                'daily_portfolio_value' => 29700, // 30000 - 1.3% dari 30000
                'daily_asset_growth' => -390, // -1.3% dari 30000
                'daily_asset_growth_percentage' => -1.3,
                'report_date' => '2025-06-04',
            ],
            [
                'user_id' => 2,
                'daily_portfolio_value' => 29610, // 29700 - 0.3% dari 29700
                'daily_asset_growth' => -90, // -0.3% dari 29700
                'daily_asset_growth_percentage' => -0.3,
                'report_date' => '2025-06-05',
            ],
            [
                'user_id' => 2,
                'daily_portfolio_value' => 29875, // 29610 + 0.9% dari 29610
                'daily_asset_growth' => 265, // 0.9% dari 29610
                'daily_asset_growth_percentage' => 0.9,
                'report_date' => '2025-06-06',
            ],
            [
                'user_id' => 2,
                'daily_portfolio_value' => 30530, // 29875 + 2.2% dari 29875
                'daily_asset_growth' => 655, // 2.2% dari 29875
                'daily_asset_growth_percentage' => 2.2,
                'report_date' => '2025-06-07',
            ],
            [
                'user_id' => 2,
                'daily_portfolio_value' => 30317, // 30530 - 0.7% dari 30530
                'daily_asset_growth' => -213, // -0.7% dari 30530
                'daily_asset_growth_percentage' => -0.7,
                'report_date' => '2025-06-08',
            ],
            [
                'user_id' => 2,
                'daily_portfolio_value' => 31137, // 30317 + 2.7% dari 30317
                'daily_asset_growth' => 820, // 2.7% dari 30317
                'daily_asset_growth_percentage' => 2.7,
                'report_date' => '2025-06-09',
            ],
            [
                'user_id' => 2,
                'daily_portfolio_value' => 32071, // 31137 + 3.0% dari 31137
                'daily_asset_growth' => 934, // 3.0% dari 31137
                'daily_asset_growth_percentage' => 3.0,
                'report_date' => '2025-06-10',
            ],
        ];

        foreach ($reports as $report) {
            \App\Models\Report::updateOrCreate(
                [
                    'user_id' => $report['user_id'],
                    'report_date' => $report['report_date'],
                ],
                [
                    'daily_portfolio_value' => $report['daily_portfolio_value'],
                    'daily_asset_growth' => $report['daily_asset_growth'],
                    'daily_asset_growth_percentage' => $report['daily_asset_growth_percentage'],
                ]
            );
        }
    }
}
