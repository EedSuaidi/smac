<?php

namespace App\Filament\Resources;

use App\Filament\Exports\TransactionExporter;
use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Currency; // Tambahkan ini
use App\Models\WalletBalance; // Tambahkan ini

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationGroup = 'Menu';

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('currency_id')
                    ->label('Currency')
                    ->placeholder('Select currency')
                    ->relationship('currency', 'name')
                    ->getOptionLabelFromRecordUsing(fn($record) => "{$record->name} ({$record->symbol})")
                    ->searchable()
                    ->getSearchResultsUsing(function (string $search) {
                        return \App\Models\Currency::query()
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('symbol', 'like', "%{$search}%")
                            ->limit(50)
                            ->get(['id', 'name', 'symbol'])
                            ->mapWithKeys(fn($currency) => [$currency->id => "{$currency->name} ({$currency->symbol})"]);
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $currency = \App\Models\Currency::find($state);
                        if ($currency && $currency->currency_type === 'fiat') {
                            $set('price', 1.00);
                            $set('total', $get('amount') ?? 0);
                        } else {
                            // Reset price dan total jika bukan fiat, agar user input
                            $set('price', null);
                            $set('total', null);
                        }
                    }),

                Forms\Components\Hidden::make('user_id')
                    ->default(fn() => auth()->id())
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->label('Amount')
                    ->placeholder('Enter amount of currency')
                    ->numeric()
                    ->required()
                    ->reactive()
                    ->debounce('500ms')
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $price = $get('price') ?? 0;
                        $total = (float) $state * (float) $price;
                        $set('total', number_format($total, 8, '.', '')); // Pastikan presisi untuk kripto
                    })
                    ->rule(function ($get) {
                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                            $currencyId = $get('currency_id');
                            $transactionType = $get('transaction_type');
                            $amount = (float) $value;

                            if (!$currencyId) {
                                return; // Skip validation if currency is not selected yet
                            }

                            $currency = \App\Models\Currency::find($currencyId);

                            if (!$currency) {
                                return; // Skip if currency not found
                            }

                            // Validasi saldo untuk 'sell' dan 'withdraw'
                            if (in_array($transactionType, ['sell', 'withdraw'])) {
                                $wallet = \App\Models\WalletBalance::where('currency_id', $currencyId)
                                    ->where('user_id', auth()->id())
                                    ->first();

                                $balance = (float) ($wallet->balance ?? 0);

                                if ($balance < $amount) {
                                    $fail("Insufficient balance for " . $currency->symbol . ". Your current balance is " . number_format($balance, 8) . ".");
                                }
                            }
                        };
                    }),

                Forms\Components\TextInput::make('price')
                    ->label('Price')
                    ->placeholder('Enter price')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->reactive()
                    ->debounce('500ms')
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $amount = $get('amount') ?? 0;
                        $total = (float) $amount * (float) $state;
                        $set('total', number_format($total, 8, '.', '')); // Pastikan presisi untuk kripto
                    })
                    ->visible(function (Forms\Get $get) {
                        $currencyId = $get('currency_id');
                        if (!$currencyId) return true;
                        $currency = \App\Models\Currency::find($currencyId);
                        return !($currency && $currency->currency_type === 'fiat');
                    }),

                Forms\Components\TextInput::make('total')
                    ->label('Total')
                    ->placeholder('Total amount will be calculated automatically')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->readOnly()
                    ->visible(function (Forms\Get $get) {
                        $currencyId = $get('currency_id');
                        if (!$currencyId) return true;
                        $currency = \App\Models\Currency::find($currencyId);
                        return !($currency && $currency->currency_type === 'fiat');
                    })
                    ->rule(function ($get) {
                        return function (string $attribute, $value, \Closure $fail) use ($get) {
                            $currencyId = $get('currency_id');
                            $transactionType = $get('transaction_type');
                            $total = (float) $value;

                            if (!$currencyId) {
                                return;
                            }

                            $currency = \App\Models\Currency::find($currencyId);

                            if (!$currency) {
                                return;
                            }

                            // Validasi saldo USD hanya untuk transaksi 'buy' kripto
                            if ($currency->currency_type === 'crypto' && $transactionType === 'buy') {
                                $usdCurrency = \App\Models\Currency::where('symbol', 'USD')->first();
                                if (!$usdCurrency) {
                                    $fail("USD currency not found in database.");
                                    return;
                                }

                                $usdWalletBalance = \App\Models\WalletBalance::where('user_id', auth()->id())
                                    ->where('currency_id', $usdCurrency->id)
                                    ->first();

                                $usdBalance = (float) ($usdWalletBalance->balance ?? 0);

                                if ($usdBalance < $total) {
                                    $fail("Insufficient USD balance to buy " . $currency->symbol . ". Your current USD balance is $" . number_format($usdBalance, 2) . ".");
                                }
                            }
                        };
                    }),

                Forms\Components\ToggleButtons::make('transaction_type')
                    ->label('Transaction Type')
                    ->inline()
                    ->required()
                    ->reactive()
                    ->options(function (Forms\Get $get) {
                        $currencyId = $get('currency_id');
                        $currency = $currencyId ? \App\Models\Currency::find($currencyId) : null;

                        if (!$currencyId || ($currency && $currency->currency_type === 'crypto')) {
                            return [
                                'buy' => 'Buy',
                                'sell' => 'Sell',
                            ];
                        } elseif ($currency && $currency->currency_type === 'fiat' && $currency->symbol === 'USD') {
                            return [
                                'deposit' => 'Deposit',
                                'withdraw' => 'Withdraw',
                            ];
                        }
                        return [];
                    })
                    ->colors(function (Forms\Get $get) {
                        $currencyId = $get('currency_id');
                        $currency = $currencyId ? \App\Models\Currency::find($currencyId) : null;

                        if (!$currencyId || ($currency && $currency->currency_type === 'crypto')) {
                            return [
                                'buy' => 'success',
                                'sell' => 'danger',
                            ];
                        } elseif ($currency && $currency->currency_type === 'fiat' && $currency->symbol === 'USD') {
                            return [
                                'deposit' => 'success',
                                'withdraw' => 'danger',
                            ];
                        }
                        return [];
                    })
                    ->icons(function (Forms\Get $get) {
                        $currencyId = $get('currency_id');
                        $currency = $currencyId ? \App\Models\Currency::find($currencyId) : null;

                        if (!$currencyId || ($currency && $currency->currency_type === 'crypto')) {
                            return [
                                'buy' => 'heroicon-o-arrow-trending-up',
                                'sell' => 'heroicon-o-arrow-trending-down',
                            ];
                        } elseif ($currency && $currency->currency_type === 'fiat' && $currency->symbol === 'USD') {
                            return [
                                'deposit' => 'heroicon-o-arrow-trending-up',
                                'withdraw' => 'heroicon-o-arrow-trending-down',
                            ];
                        }
                        return [];
                    }),

                Forms\Components\DatePicker::make('transaction_date')
                    ->label('Transaction Date')
                    ->default(now())
                    ->required(),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc');
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
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return number_format($state, 2); // Sesuaikan dengan presisi decimal
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return '$' . number_format($state, 2); // Sesuaikan dengan presisi decimal
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return '$' . number_format($state, 2); // Sesuaikan dengan presisi decimal
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('transaction_type')
                    ->label('Transaction Type')
                    ->sortable()
                    ->toggleable()
                    ->badge()
                    ->icons([
                        'heroicon-o-arrow-trending-up' => 'buy',
                        'heroicon-o-arrow-trending-down' => 'sell',
                        'heroicon-o-arrows-pointing-in' => 'deposit',
                        'heroicon-o-arrows-pointing-out' => 'withdraw',
                    ])
                    ->colors([
                        'success' => 'buy',
                        'danger' => 'sell',
                        'blue' => 'deposit',
                        'warning' => 'withdraw',
                    ])
                    ->formatStateUsing(fn($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Transaction Date')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('transaction_type')
                    ->label('Transaction Type')
                    ->options([
                        'buy' => 'Buy',
                        'sell' => 'Sell',
                        'deposit' => 'Deposit',
                        'withdraw' => 'Withdraw',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (isset($data['value'])) {
                            $query->where('transaction_type', $data['value']);
                        }
                    }),

                Tables\Filters\Filter::make('transaction_date')
                    ->form([
                        Forms\Components\DatePicker::make('from'),
                        Forms\Components\DatePicker::make('until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('transaction_date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('transaction_date', '<=', $date),
                            );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
                Tables\Actions\ExportBulkAction::make()
                    ->exporter(TransactionExporter::class),
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
            // 'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
