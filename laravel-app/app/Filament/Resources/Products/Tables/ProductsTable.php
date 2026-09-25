<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('type')
                    ->label('Product')
                    ->badge()
                    ->color('primary')
                    ->searchable(),
                TextColumn::make('base_price_per_liter')
                    ->label('Selling price')
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('buying_price_per_liter')
                    ->label('Buying price')
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('tanks_count')
                    ->label('Tanks')
                    ->counts('tanks')
                    ->badge()
                    ->color('gray'),
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
