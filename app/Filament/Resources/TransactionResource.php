<?php

namespace App\Filament\Resources;

use App\Filament\Exports\TransactionExporter;
use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

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
                            ->orWhere('symbol', 'like', "%{$search}%") // Tambahkan pencarian berdasarkan symbol
                            ->limit(50)
                            ->get(['id', 'name', 'symbol']) // Ambil kolom 'id', 'name', dan 'symbol' langsung dari database
                            ->mapWithKeys(fn($currency) => [$currency->id => "{$currency->name} ({$currency->symbol})"]);
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        // // Reset type, price, total saat currency_id berubah
                        // $set('transaction_type', null);
                        // $set('price', null);
                        // $set('total', null);

                        $currency = \App\Models\Currency::find($state);
                        if ($currency && $currency->currency_type === 'fiat') {
                            $set('price', 1.00); // Harga USD adalah 1
                            $set('total', $get('amount') ?? 0); // <-- TAMBAHKAN INI: total = amount
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
                        // Pastikan perhitungan menggunakan nilai float/decimal dan format total
                        $total = (float) $state * (float) $price;
                        $set('total', number_format($total, 2, '.', '')); // Format ke 2 desimal
                    })
                    ->rule(function ($get) {
                        // Validasi saldo untuk 'sell' dan 'withdraw'
                        if (in_array($get('transaction_type'), ['sell', 'withdraw'])) {
                            $wallet = \App\Models\WalletBalance::where('currency_id', $get('currency_id'))
                                ->where('user_id', auth()->id())
                                ->first();

                            $balance = $wallet->balance ?? 0;

                            return "lte:{$balance}";
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
                    ->debounce('500ms')
                    ->afterStateUpdated(function ($state, callable $set, $get) {
                        $amount = $get('amount') ?? 0;
                        // Pastikan perhitungan menggunakan nilai float/decimal dan format total
                        $total = (float) $amount * (float) $state;
                        $set('total', number_format($total, 2, '.', '')); // Format ke 2 desimal
                    })
                    ->visible(function (Forms\Get $get) {
                        $currencyId = $get('currency_id');
                        // Secara default terlihat, hanya disembunyikan jika mata uang USD dipilih
                        if (!$currencyId) return true; // Default terlihat jika belum memilih currency
                        $currency = \App\Models\Currency::find($currencyId);
                        // Sembunyikan jika itu USD
                        return !($currency && $currency->currency_type === 'fiat');
                    }),

                Forms\Components\TextInput::make('total')
                    ->label('Total')
                    ->placeholder('Total amount will be calculated automatically')
                    ->numeric()
                    ->prefix('$')
                    ->required()
                    ->readOnly()
                    ->visible(function (Forms\Get $get) { // <-- TAMBAHKAN BLOK INI
                        $currencyId = $get('currency_id');
                        if (!$currencyId) return true; // Default visible jika belum memilih currency
                        $currency = \App\Models\Currency::find($currencyId);
                        // Sembunyikan jika itu USD
                        return !($currency && $currency->currency_type === 'fiat');
                    }),

                Forms\Components\ToggleButtons::make('transaction_type')
                    ->label('Transaction Type')
                    ->inline()
                    ->required()
                    ->reactive()
                    ->options(function (Forms\Get $get) {
                        $currencyId = $get('currency_id');
                        $currency = $currencyId ? \App\Models\Currency::find($currencyId) : null;

                        // Default: Buy/Sell jika tidak ada mata uang dipilih atau mata uangnya kripto
                        if (!$currencyId || ($currency && $currency->currency_type === 'crypto')) {
                            return [
                                'buy' => 'Buy',
                                'sell' => 'Sell',
                            ];
                        }
                        // Jika mata uangnya USD Fiat
                        elseif ($currency && $currency->currency_type === 'fiat' && $currency->symbol === 'USD') {
                            return [
                                'deposit' => 'Deposit',
                                'withdraw' => 'Withdraw',
                            ];
                        }
                        return []; // Fallback, seharusnya tidak tercapai
                    })
                    ->colors(function (Forms\Get $get) {
                        $currencyId = $get('currency_id');
                        $currency = $currencyId ? \App\Models\Currency::find($currencyId) : null;
                        $selectedType = $get('transaction_type'); // Dapatkan tipe yang sedang terpilih

                        // Default: Buy/Sell jika tidak ada mata uang dipilih atau mata uangnya kripto
                        if (!$currencyId || ($currency && $currency->currency_type === 'crypto')) {
                            return [
                                'buy' => 'success',
                                'sell' => 'danger',
                            ];
                        }
                        // Jika mata uangnya USD Fiat
                        elseif ($currency && $currency->currency_type === 'fiat' && $currency->symbol === 'USD') {
                            return [
                                'deposit' => 'success',
                                'withdraw' => 'danger',
                            ];
                        }
                        return []; // Fallback
                    })
                    ->icons(function (Forms\Get $get) {
                        $currencyId = $get('currency_id');
                        $currency = $currencyId ? \App\Models\Currency::find($currencyId) : null;
                        $selectedType = $get('transaction_type'); // Dapatkan tipe yang sedang terpilih

                        // Default: Buy/Sell jika tidak ada mata uang dipilih atau mata uangnya kripto
                        if (!$currencyId || ($currency && $currency->currency_type === 'crypto')) {
                            return [
                                'buy' => 'heroicon-o-arrow-trending-up',
                                'sell' => 'heroicon-o-arrow-trending-down',
                            ];
                        }
                        // Jika mata uangnya USD Fiat
                        elseif ($currency && $currency->currency_type === 'fiat' && $currency->symbol === 'USD') {
                            return [
                                'deposit' => 'heroicon-o-arrow-trending-up',
                                'withdraw' => 'heroicon-o-arrow-trending-down',
                            ];
                        }
                        return []; // Fallback
                    }),

                Forms\Components\DatePicker::make('transaction_date')
                    ->label('Transaction Date')
                    ->default(now())
                    ->required(),
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
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return number_format($state, 2);
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return '$' . number_format($state, 2);
                    })
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        return '$' . number_format($state, 2);
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
                SelectFilter::make('transaction_type') // Gunakan SelectFilter
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
