<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationGroup = 'Menu';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->placeholder('Enter currency name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('symbol')
                    ->label('Symbol')
                    ->placeholder('Enter currency symbol')
                    ->required()
                    ->maxLength(10)
                    ->unique(ignoreRecord: true),
                Forms\Components\ToggleButtons::make('currency_type')
                    ->label('Currency Type')
                    ->inline()
                    ->required()
                    ->options([
                        'fiat' => 'Fiat',
                        'crypto' => 'Crypto',
                    ])
                    ->colors([
                        'fiat' => 'success',
                        'crypto' => 'warning',
                    ])
                    ->icons([
                        'fiat' => 'heroicon-o-currency-dollar',
                        'crypto' => 'heroicon-o-globe-alt',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('symbol')
                    ->label('Symbol')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('currency_type')
                    ->label('Currency Type')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->icons([
                        'heroicon-o-currency-dollar' => 'fiat',
                        'heroicon-o-globe-alt' => 'crypto',
                    ])
                    ->colors([
                        'success' => 'fiat',
                        'warning' => 'crypto',
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state)),
            ])
            ->filters([
                //
            ])
            // ->headerActions([
            //     ExportAction::make()->exporter(CurrencyExporter::class),
            // ])
            ->actions([
                Tables\Actions\ViewAction::make()->color('blue'),
                Tables\Actions\EditAction::make()->color('warning'),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListCurrencys::route('/'),
            'create' => Pages\CreateCurrency::route('/create'),
            'edit' => Pages\EditCurrency::route('/{record}/edit'),
        ];
    }
}
