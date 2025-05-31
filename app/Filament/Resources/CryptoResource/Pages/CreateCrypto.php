<?php

namespace App\Filament\Resources\CryptoResource\Pages;

use App\Filament\Resources\CryptoResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCrypto extends CreateRecord
{
    protected static string $resource = CryptoResource::class;

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
