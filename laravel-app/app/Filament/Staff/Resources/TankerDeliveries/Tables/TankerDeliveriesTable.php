<?php

namespace App\Filament\Staff\Resources\TankerDeliveries\Tables;

use App\Models\TankerDelivery;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TankerDeliveriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanker_plate_number')
                    ->searchable(),
                TextColumn::make('supplier_name')
                    ->searchable(),
                TextColumn::make('invoice_number')
                    ->searchable(),
                TextColumn::make('product.type')
                    ->label('Product')
                    ->searchable(),
                TextColumn::make('tank.name')
                    ->searchable(),
                TextColumn::make('waybill_liters')
                    ->suffix(' L')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('received_liters')
                    ->suffix(' L')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('variance_liters')
                    ->suffix(' L')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('buying_price_per_liter')
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('total_cost')
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        TankerDelivery::STATUS_CONFIRMED => 'success',
                        TankerDelivery::STATUS_REJECTED => 'danger',
                        default => 'warning',
                    })
                    ->searchable(),
                TextColumn::make('confirmedBy.name')
                    ->label('Reviewed by')
                    ->searchable(),
                TextColumn::make('confirmed_at')
                    ->dateTime()
                    ->sortable(),
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
                EditAction::make()
                    ->visible(fn (TankerDelivery $record): bool => $record->status === TankerDelivery::STATUS_PENDING),
                DeleteAction::make()
                    ->visible(fn (TankerDelivery $record): bool => $record->status === TankerDelivery::STATUS_PENDING),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
