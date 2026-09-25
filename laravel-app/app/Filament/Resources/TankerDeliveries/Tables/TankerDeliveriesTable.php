<?php

namespace App\Filament\Resources\TankerDeliveries\Tables;

use App\Models\TankerDelivery;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TankerDeliveriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tanker_plate_number')
                    ->label('Plate number')
                    ->searchable(),
                TextColumn::make('supplier_name')
                    ->label('Supplier')
                    ->searchable(),
                TextColumn::make('invoice_number')
                    ->searchable(),
                TextColumn::make('product.type')
                    ->label('Product')
                    ->searchable(),
                TextColumn::make('tank.name')
                    ->label('Tank')
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
                    ->label('Variance')
                    ->suffix(' L')
                    ->numeric()
                    ->sortable()
                    ->color(fn (TankerDelivery $record): ?string => (float) $record->variance_liters !== 0.0 ? 'danger' : null)
                    ->weight(fn (TankerDelivery $record): ?string => (float) $record->variance_liters !== 0.0 ? 'bold' : null),
                TextColumn::make('buying_price_per_liter')
                    ->label('Buying price')
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
                TextColumn::make('createdBy.name')
                    ->label('Logged by')
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
                SelectFilter::make('status')
                    ->options([
                        TankerDelivery::STATUS_PENDING => 'Pending',
                        TankerDelivery::STATUS_CONFIRMED => 'Confirmed',
                        TankerDelivery::STATUS_REJECTED => 'Rejected',
                    ]),
            ])
            ->recordActions([
                Action::make('confirm')
                    ->label('Confirm')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (TankerDelivery $record): bool => $record->status === TankerDelivery::STATUS_PENDING)
                    ->schema([
                        TextInput::make('buying_price_per_liter')
                            ->label('Buying price per litre')
                            ->prefix('NGN')
                            ->numeric()
                            ->required()
                            ->default(fn (TankerDelivery $record) => $record->product->buying_price_per_liter),
                    ])
                    ->requiresConfirmation()
                    ->modalDescription(fn (TankerDelivery $record): string => "This credits {$record->received_liters} L to {$record->tank->name} and updates {$record->product->type}'s buying price.")
                    ->action(function (TankerDelivery $record, array $data): void {
                        $record->confirm(auth()->user(), (float) $data['buying_price_per_liter']);

                        Notification::make()
                            ->title('Delivery confirmed and stock updated')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (TankerDelivery $record): bool => $record->status === TankerDelivery::STATUS_PENDING)
                    ->requiresConfirmation()
                    ->modalDescription('This delivery will be marked as rejected. No stock will be added.')
                    ->action(function (TankerDelivery $record): void {
                        $record->reject(auth()->user());

                        Notification::make()
                            ->title('Delivery rejected')
                            ->warning()
                            ->send();
                    }),
                EditAction::make()
                    ->visible(fn (TankerDelivery $record): bool => $record->status === TankerDelivery::STATUS_PENDING),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['product', 'tank', 'createdBy', 'confirmedBy']));
    }
}
