<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationGroup = 'Menu';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('crypto_id')
                    ->label('Cryptocurrency')
                    ->placeholder('Select cryptocurrency')
                    ->relationship('crypto', 'name')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} ({$record->symbol})") // Format label menjadi "name (symbol)"
                    ->searchable()
                    ->required(),
                Forms\Components\Hidden::make('user_id')
                    ->default(fn() => auth()->id())
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->label('Amount')
                    ->placeholder('Enter amount of cryptocurrency')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $price = $get('price') ?? 0;
                        $set('total', $state * $price);
                    })
                    ->rule(function ($get) {
                        if ($get('type') === 'sell') {
                            $wallet = \App\Models\WalletBalance::where('crypto_id', $get('crypto_id'))
                                ->where('user_id', auth()->id())
                                ->first();

                            $balance = $wallet->balance ?? 0;

                            return "lte:{$balance}"; // Pastikan jumlah yang dijual <= balance
                        }

                        return null;
                    }),
                Forms\Components\TextInput::make('price')
                    ->label('Price')
                    ->placeholder('Enter price')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $amount = $get('amount') ?? 0;
                        $set('total', $amount * $state);
                    }),
                Forms\Components\TextInput::make('total')
                    ->label('Total')
                    ->placeholder('Total amount will be calculated automatically')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->readOnly(),
                Forms\Components\ToggleButtons::make('type')
                    ->label('Transaction Type')
                    ->inline()
                    ->options([
                        'buy' => 'Buy',
                        'sell' => 'Sell',
                    ])
                    ->colors([
                        'buy' => 'success',
                        'sell' => 'danger',
                    ])
                    ->icons([
                        'buy' => 'heroicon-o-arrow-trending-up',
                        'sell' => 'heroicon-o-arrow-trending-down',
                    ])
                    ->required(),
                Forms\Components\DatePicker::make('transaction_date')
                    ->label('Transaction Date')
                    ->default(now())
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('crypto.name')
                    ->label('Crypto')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(function ($state, $record) {
                        $symbol = $record->crypto->symbol ?? '';
                        return "<div>{$state}<br><small style='font-size: 0.8em; color: gray;'>{$symbol}</small></div>";
                    })
                    ->html(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return '$ ' . number_format($state, 2);
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return '$ ' . number_format($state, 2);
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return '$ ' . number_format($state, 2);
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Transaction Type')
                    ->badge()
                    ->icons([
                        'heroicon-o-arrow-trending-up' => 'buy',
                        'heroicon-o-arrow-trending-down' => 'sell',
                    ])
                    ->colors([
                        'success' => 'buy',
                        'danger' => 'sell',
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state))
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
