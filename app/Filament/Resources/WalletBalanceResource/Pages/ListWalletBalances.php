<?php

namespace App\Filament\Resources\WalletBalanceResource\Pages;

use App\Filament\Resources\WalletBalanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWalletBalances extends ListRecords
{
    protected static string $resource = WalletBalanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
