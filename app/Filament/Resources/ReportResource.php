<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
// use App\Filament\Resources\ReportResource\RelationManagers;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
// use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;

    protected static ?string $navigationGroup = 'Menu';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 4;

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
                Tables\Columns\TextColumn::make('daily_portfolio_value')
                    ->label('Daily Portfolio Value')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(function ($state) {
                        return '$' . number_format($state, 0);
                    }),
                Tables\Columns\TextColumn::make('daily_asset_growth')
                    ->label('Daily Asset Growth')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(function ($state) {
                        return '$' . number_format($state, 0);
                    })
                    ->badge()
                    ->colors([
                        'success' => fn($state) => $state >= 0,
                        'danger' => fn($state) => $state < 0,
                    ]),
                Tables\Columns\TextColumn::make('report_date')
                    ->label('Report Date')
                    ->date()
                    ->sortable()
                    ->toggleable()
                    ->formatStateUsing(function ($state) {
                        return $state->format('d M Y');
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListReports::route('/'),
            // 'create' => Pages\CreateReport::route('/create'),
            // 'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }
}
