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
                'daily_portfolio_value' => 1010000,
                'daily_asset_growth' => 10000,
                'daily_asset_growth_percentage' => 1.0,
                'report_date' => '2025-05-27',
            ],
            [
                'user_id' => 2,
                'daily_portfolio_value' => 997000,
                'daily_asset_growth' => -13000,
                'daily_asset_growth_percentage' => -1.3,
                'report_date' => '2025-05-28',
            ],
            [
                'user_id' => 2,
                'daily_portfolio_value' => 994000,
                'daily_asset_growth' => -3000,
                'daily_asset_growth_percentage' => -0.3,
                'report_date' => '2025-05-29',
            ],
            [
                'user_id' => 2,
                'daily_portfolio_value' => 1002800,
                'daily_asset_growth' => 8800,
                'daily_asset_growth_percentage' => 0.9,
                'report_date' => '2025-05-30',
            ],
            [
                'user_id' => 2,
                'daily_portfolio_value' => 1025000,
                'daily_asset_growth' => 22200,
                'daily_asset_growth_percentage' => 2.2,
                'report_date' => '2025-05-31',
            ],
            [
                'user_id' => 2,
                'daily_portfolio_value' => 1018000,
                'daily_asset_growth' => -7000,
                'daily_asset_growth_percentage' => -0.7,
                'report_date' => '2025-06-01',
            ],
            [
                'user_id' => 2,
                'daily_portfolio_value' => 1045000,
                'daily_asset_growth' => 27000,
                'daily_asset_growth_percentage' => 2.7,
                'report_date' => '2025-06-02',
            ],
            [
                'user_id' => 2,
                'daily_portfolio_value' => 1075000,
                'daily_asset_growth' => 30000,
                'daily_asset_growth_percentage' => 3.0,
                'report_date' => '2025-06-03',
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
