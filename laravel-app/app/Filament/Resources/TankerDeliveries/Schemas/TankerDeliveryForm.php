<?php

namespace App\Filament\Resources\TankerDeliveries\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class TankerDeliveryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Shipment details')
                    ->description('Who delivered it, and which tank it belongs to.')
                    ->icon('heroicon-o-truck')
                    ->columns(2)
                    ->schema([
                        TextInput::make('tanker_plate_number')
                            ->label('Tanker plate number')
                            ->required(),
                        TextInput::make('supplier_name')
                            ->label('Supplier name')
                            ->required(),
                        TextInput::make('invoice_number')
                            ->label('Invoice / waybill number')
                            ->required(),
                        Select::make('created_by_id')
                            ->label('Logged by')
                            ->relationship('createdBy', 'name')
                            ->required(),
                        Select::make('product_id')
                            ->label('Product type')
                            ->relationship('product', 'type')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('tank_id')
                            ->label('Receiving tank')
                            ->relationship('tank', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('Quantity verification')
                    ->description('Compare the waybill quantity with the litres discharged after tank dipping.')
                    ->icon('heroicon-o-scale')
                    ->columns(3)
                    ->schema([
                        TextInput::make('waybill_liters')
                            ->label('Waybill quantity')
                            ->suffix('L')
                            ->required()
                            ->numeric()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateVariance($get, $set)),
                        TextInput::make('received_liters')
                            ->label('Received / discharged')
                            ->suffix('L')
                            ->required()
                            ->numeric()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateVariance($get, $set)),
                        TextInput::make('variance_liters')
                            ->label('Variance')
                            ->suffix('L')
                            ->numeric()
                            ->readOnly()
                            ->dehydrated()
                            ->default(0)
                            ->helperText('Waybill litres minus received litres. Calculated automatically.'),
                    ]),

                Section::make('Notes')
                    ->description('Buying price, total cost, and stock movement are set when this delivery is confirmed from the deliveries list — not here.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Textarea::make('driver_notes')
                            ->label('Driver / delivery notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Hidden::make('status')->default('PENDING'),
            ]);
    }

    private static function updateVariance(Get $get, Set $set): void
    {
        $waybill = (float) ($get('waybill_liters') ?? 0);
        $received = (float) ($get('received_liters') ?? 0);

        $set('variance_liters', round($waybill - $received, 2));
    }
}
