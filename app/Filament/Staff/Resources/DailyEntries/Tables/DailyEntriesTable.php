<?php

namespace App\Filament\Staff\Resources\DailyEntries\Tables;

use App\Models\DailyEntry;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class DailyEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('entry_date', 'desc')
            ->columns([
                TextColumn::make('entry_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color('info'),
                TextColumn::make('fuel_grand_total_liters')
                    ->label('Litres sold')
                    ->suffix(' L')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('fuel_grand_total_amount')
                    ->label('Fuel revenue')
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('total_expenses')
                    ->label('Expenses')
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('net_cash_revenue')
                    ->label('Net cash revenue')
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('net_profit')
                    ->label('Net profit / loss')
                    ->money('NGN')
                    ->color(fn (DailyEntry $record): ?string => (float) $record->net_profit < 0 ? 'danger' : 'success')
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('cash_amount')
                    ->label('Cash')
                    ->money('NGN')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('pos_amount')
                    ->label('POS / transfer')
                    ->money('NGN')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('bank_deposit_amount')
                    ->label('Bank deposit')
                    ->money('NGN')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('gross_profit')
                    ->label('Gross profit')
                    ->money('NGN')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
