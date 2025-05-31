<?php

namespace App\Filament\Resources\CryptoResource\Pages;

use App\Filament\Resources\CryptoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCryptos extends ListRecords
{
    protected static string $resource = CryptoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
