<?php

namespace App\Filament\Resources\Tanks\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TankForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tank identity')
                    ->description('What the tank is called and which product it holds.')
                    ->icon('heroicon-o-circle-stack')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->placeholder('e.g. PMS Tank 1')
                            ->required(),
                        Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'type')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('Capacity & stock')
                    ->description('Stock levels normally move only through confirmed tanker deliveries — edit the current stock directly here only to correct a dipping error.')
                    ->icon('heroicon-o-scale')
                    ->columns(2)
                    ->schema([
                        TextInput::make('capacity_liters')
                            ->label('Capacity')
                            ->suffix('L')
                            ->required()
                            ->numeric()
                            ->minValue(0),
                        TextInput::make('current_stock_liters')
                            ->label('Current stock')
                            ->suffix('L')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0),
                    ]),

                Section::make('Status')
                    ->icon('heroicon-o-power')
                    ->schema([
                        Toggle::make('active')
                            ->label('Active')
                            ->helperText('Inactive tanks are hidden from pump setup and delivery destinations.')
                            ->default(true)
                            ->required(),
                    ]),
            ]);
    }
}
