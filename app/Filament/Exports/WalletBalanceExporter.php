<?php

namespace App\Filament\Exports;

use App\Models\WalletBalance;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class WalletBalanceExporter extends Exporter
{
    protected static ?string $model = WalletBalance::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('currency.name')
                ->label('Currency Name'),
            ExportColumn::make('currency.symbol')
                ->label('Currency Symbol'),
            ExportColumn::make('balance')
                ->label('Balance'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your wallet balance export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
