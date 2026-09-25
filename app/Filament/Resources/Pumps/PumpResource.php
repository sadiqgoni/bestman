<?php

namespace App\Filament\Resources\Pumps;

use App\Filament\Resources\Pumps\Pages\CreatePump;
use App\Filament\Resources\Pumps\Pages\EditPump;
use App\Filament\Resources\Pumps\Pages\ListPumps;
use App\Filament\Resources\Pumps\Schemas\PumpForm;
use App\Filament\Resources\Pumps\Tables\PumpsTable;
use App\Models\Pump;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PumpResource extends Resource
{
    protected static ?string $model = Pump::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    protected static string|\UnitEnum|null $navigationGroup = 'Inventory & Pricing';

    protected static ?string $navigationLabel = 'Pumps';

    protected static ?int $navigationSort = 30;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return PumpForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PumpsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPumps::route('/'),
            'create' => CreatePump::route('/create'),
            'edit' => EditPump::route('/{record}/edit'),
        ];
    }
}
