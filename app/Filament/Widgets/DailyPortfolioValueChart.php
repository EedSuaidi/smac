<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Report; // Import model Report
use Illuminate\Support\Facades\Auth; // Untuk mendapatkan user yang login
use Carbon\Carbon; // Untuk manipulasi tanggal
use Illuminate\Support\Facades\Cache; // Opsional: Untuk caching data chart
use Illuminate\Support\Facades\Log;

class DailyPortfolioValueChart extends ChartWidget
{
    protected static ?string $heading = 'Daily Portfolio Value (Last 7 Days)';

    protected static ?int $sort = 2; // Atur urutan agar berada di bawah stats

    // protected static ?string $pollingInterval = '30s';

    protected function getType(): string
    {
        return 'bar'; // Jenis grafik: 'line', 'bar', 'pie', dll.
    }

    public static function canView(): bool
    {
        // Hanya user dengan role 'user' yang dapat melihat widget ini
        return auth()->user()->role === 'user';
    }

    protected function getData(): array
    {
        $userId = Auth::id(); // Dapatkan ID user yang sedang login

        if (!$userId) {
            return ['labels' => [], 'datasets' => []]; // Kembalikan array kosong jika tidak ada user login
        }

        // Ambil data selama 7 hari terakhir
        $reports = Report::query()
            ->where('user_id', $userId)
            ->where('report_date', '>=', now()->subDays(7)->toDateString())
            ->orderBy('report_date')
            ->get(['report_date', 'daily_portfolio_value']);

        // Format data untuk chart
        $labels = $reports->pluck('report_date')->map(fn($date) => date('d M', strtotime($date)))->toArray();
        $values = $reports->pluck('daily_portfolio_value')->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Portfolio Value',
                    'data' => $values,
                    'borderColor' => '#009966', // Warna garis
                    'backgroundColor' => 'rgba(0,188,125, 0.2)', // Warna area di bawah garis
                ],
            ],
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => false, // Sumbu Y tidak dimulai dari 0
                ],
            ],
        ];
    }
}
