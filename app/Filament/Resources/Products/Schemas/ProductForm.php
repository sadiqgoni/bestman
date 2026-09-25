<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product')
                    ->description('The fuel type sold at the station.')
                    ->icon('heroicon-o-tag')
                    ->schema([
                        TextInput::make('type')
                            ->label('Product code')
                            ->placeholder('e.g. PMS, DIESEL, KEROSENE')
                            ->required()
                            ->unique(ignoreRecord: true),
                    ]),

                Section::make('Rate card')
                    ->description('The selling price is what staff use for pump readings by default. The buying price updates automatically whenever a delivery is confirmed.')
                    ->icon('heroicon-o-banknotes')
                    ->columns(2)
                    ->schema([
                        TextInput::make('base_price_per_liter')
                            ->label('Selling price / litre')
                            ->prefix('NGN')
                            ->required()
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('buying_price_per_liter')
                            ->label('Current buying price / litre')
                            ->prefix('NGN')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ]),
            ]);
    }
}
