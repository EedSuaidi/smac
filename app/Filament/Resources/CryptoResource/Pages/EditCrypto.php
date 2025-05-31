<?php

namespace App\Filament\Resources\CryptoResource\Pages;

use App\Filament\Resources\CryptoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCrypto extends EditRecord
{
    protected static string $resource = CryptoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
