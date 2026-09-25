<?php

namespace App\Filament\Resources\Pumps\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PumpForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pump identity')
                    ->description('What the pump is called on the forecourt.')
                    ->icon('heroicon-o-bolt')
                    ->schema([
                        TextInput::make('name')
                            ->placeholder('e.g. Pump 1 (PMS)')
                            ->required(),
                    ]),

                Section::make('Assignment')
                    ->description('Which product this pump dispenses and the tank it draws from.')
                    ->icon('heroicon-o-circle-stack')
                    ->columns(2)
                    ->schema([
                        Select::make('product_id')
                            ->label('Product')
                            ->relationship('product', 'type')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Select::make('tank_id')
                            ->label('Feeding tank')
                            ->relationship('tank', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ]),

                Section::make('Status')
                    ->icon('heroicon-o-power')
                    ->schema([
                        Toggle::make('active')
                            ->label('Active')
                            ->helperText('Only active pumps appear on the staff daily-entry form for meter readings.')
                            ->default(true)
                            ->required(),
                    ]),
            ]);
    }
}
