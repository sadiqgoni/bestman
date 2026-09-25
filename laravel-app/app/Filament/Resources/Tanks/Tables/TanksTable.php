<?php

namespace App\Filament\Resources\Tanks\Tables;

use App\Models\Tank;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TanksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('product.type')
                    ->label('Product')
                    ->badge()
                    ->color('primary')
                    ->searchable(),
                TextColumn::make('capacity_liters')
                    ->label('Capacity')
                    ->suffix(' L')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('current_stock_liters')
                    ->label('Stock')
                    ->suffix(' L')
                    ->numeric()
                    ->sortable()
                    ->color(fn (Tank $record): ?string => (float) $record->capacity_liters > 0 && ((float) $record->current_stock_liters / (float) $record->capacity_liters) < 0.15 ? 'danger' : null)
                    ->description(fn (Tank $record): ?string => (float) $record->capacity_liters > 0
                        ? number_format(((float) $record->current_stock_liters / (float) $record->capacity_liters) * 100, 0) . '% full'
                        : null),
                IconColumn::make('active')
                    ->boolean(),
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
                TernaryFilter::make('active'),
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
