<?php

namespace App\Filament\Staff\Resources\TankerDeliveries\Schemas;

use Filament\Forms\Components\DateTimePicker;
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
                    ->description('Record the delivery documents and product received at the station.')
                    ->icon('heroicon-o-truck')
                    ->columns(2)
                    ->schema([
                        TextInput::make('tanker_plate_number')
                            ->label('Tanker plate number')
                            ->placeholder('e.g. XA 123 ABC')
                            ->required(),
                        TextInput::make('supplier_name')
                            ->label('Supplier name')
                            ->required(),
                        TextInput::make('invoice_number')
                            ->label('Invoice / waybill number')
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
                            ->numeric()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateVariance($get, $set)),
                        TextInput::make('received_liters')
                            ->label('Received / discharged')
                            ->suffix('L')
                            ->numeric()
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => self::updateVariance($get, $set)),
                        TextInput::make('variance_liters')
                            ->label('Shortage / variance')
                            ->suffix('L')
                            ->numeric()
                            ->default(0)
                            ->helperText('Waybill litres minus received litres. Calculated automatically.')
                            ->readOnly()
                            ->dehydrated()
                            ->required(),
                    ]),

                Section::make('Cost and delivery notes')
                    ->description('Add purchase costing and any observations for Admin review.')
                    ->icon('heroicon-o-document-text')
                    ->columns(2)
                    ->schema([
                        TextInput::make('buying_price_per_liter')
                            ->label('Buying price per litre')
                            ->prefix('NGN')
                            ->numeric(),
                        TextInput::make('total_cost')
                            ->label('Total purchase cost')
                            ->prefix('NGN')
                            ->numeric(),
                        Textarea::make('driver_notes')
                            ->label('Driver / delivery notes')
                            ->placeholder('Add seal details, dipping notes, or other observations.')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Hidden::make('status')->default('PENDING'),
                Hidden::make('created_by_id')->default(fn () => auth('staff')->id()),
                Hidden::make('confirmed_by_id'),
                DateTimePicker::make('confirmed_at')->hidden(),
            ]);
    }

    private static function updateVariance(Get $get, Set $set): void
    {
        $waybill = (float) ($get('waybill_liters') ?? 0);
        $received = (float) ($get('received_liters') ?? 0);

        $set('variance_liters', round($waybill - $received, 2));
    }
}
