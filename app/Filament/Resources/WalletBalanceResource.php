<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletBalanceResource\Pages;
// use App\Filament\Resources\WalletBalanceResource\RelationManagers;
use App\Models\WalletBalance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\SoftDeletingScope;

class WalletBalanceResource extends Resource
{
    protected static ?string $model = WalletBalance::class;

    protected static ?string $navigationGroup = 'Menu';

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        // Panggil parent query untuk mendapatkan query dasar
        // Kemudian tambahkan kondisi where untuk user_id yang sedang login
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('currency.name')
                    ->label('Currency')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(function ($state, $record) {
                        $symbol = $record->currency->symbol ?? '';
                        return "<div>{$state}<br><small style='font-size: 0.8em; color: gray;'>{$symbol}</small></div>";
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('balance')
                    ->label('Balance')
                    ->sortable()
                    ->numeric(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->getStateUsing(function ($record) {
                        // Gunakan symbol dari database, bukan coingecko_id
                        $currencySymbol = $record->currency->symbol;
                        // Pastikan Anda sudah mengubah singleton di AppServiceProvider
                        return app(\App\Services\CryptoPriceService::class)->getPrice($currencySymbol);
                    })
                    ->formatStateUsing(fn($state) => '$' . number_format($state, 2)),
                Tables\Columns\TextColumn::make('total_asset')
                    ->label('Total Asset')
                    ->getStateUsing(function ($record) {
                        // Gunakan symbol dari database, bukan coingecko_id
                        $currencySymbol = $record->currency->symbol;
                        // Pastikan Anda sudah mengubah singleton di AppServiceProvider
                        $price = app(\App\Services\CryptoPriceService::class)->getPrice($currencySymbol);
                        return $record->balance * $price;
                    })
                    ->formatStateUsing(fn($state) => '$' . number_format($state, 2)),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWalletBalances::route('/'),
            // 'create' => Pages\CreateWalletBalance::route('/create'),
            // 'edit' => Pages\EditWalletBalance::route('/{record}/edit'),
        ];
    }
}
