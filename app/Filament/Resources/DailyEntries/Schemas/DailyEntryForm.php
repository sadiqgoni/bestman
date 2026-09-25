<?php

namespace App\Filament\Resources\DailyEntries\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DailyEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Entry details')
                    ->description('Which staff member this record belongs to, and its review status.')
                    ->icon('heroicon-o-calendar-days')
                    ->columns(3)
                    ->schema([
                        DatePicker::make('entry_date')
                            ->label('Entry date')
                            ->required(),
                        Select::make('staff_id')
                            ->label('Staff')
                            ->relationship('staff', 'name')
                            ->required(),
                        TextInput::make('status')
                            ->required()
                            ->default('SUBMITTED')
                            ->readOnly(),
                    ]),

                Section::make('Sales summary')
                    ->description('Totals are calculated by the staff portal from individual pump readings and are read-only here.')
                    ->icon('heroicon-o-chart-bar-square')
                    ->columns(2)
                    ->schema([
                        TextInput::make('fuel_grand_total_liters')
                            ->label('Total litres sold')
                            ->suffix('L')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(),
                        TextInput::make('fuel_grand_total_amount')
                            ->label('Total fuel revenue')
                            ->prefix('NGN')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(),
                    ]),

                Section::make('Payment breakdown')
                    ->description('How the fuel revenue was received, as reconciled by staff.')
                    ->icon('heroicon-o-banknotes')
                    ->columns(3)
                    ->schema([
                        TextInput::make('cash_amount')
                            ->label('Physical cash')
                            ->prefix('NGN')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(),
                        TextInput::make('pos_amount')
                            ->label('POS / transfer')
                            ->prefix('NGN')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(),
                        TextInput::make('bank_deposit_amount')
                            ->label('Bank deposit')
                            ->prefix('NGN')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(),
                    ]),

                Section::make('Expenses & profit')
                    ->description('Net cash, gross profit, and net profit are derived from the sales, payments, and expenses above.')
                    ->icon('heroicon-o-calculator')
                    ->columns(2)
                    ->schema([
                        TextInput::make('total_expenses')
                            ->label('Total expenses')
                            ->prefix('NGN')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(),
                        TextInput::make('net_cash_revenue')
                            ->label('Net cash revenue')
                            ->prefix('NGN')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(),
                        TextInput::make('gross_profit')
                            ->label('Gross profit')
                            ->prefix('NGN')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(),
                        TextInput::make('net_profit')
                            ->label('Net profit / loss')
                            ->prefix('NGN')
                            ->numeric()
                            ->default(0)
                            ->readOnly()
                            ->dehydrated(),
                    ]),
            ]);
    }
}
