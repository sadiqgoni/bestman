<?php

namespace App\Filament\Staff\Resources\TankerDeliveries;

use App\Filament\Staff\Resources\TankerDeliveries\Pages\CreateTankerDelivery;
use App\Filament\Staff\Resources\TankerDeliveries\Pages\EditTankerDelivery;
use App\Filament\Staff\Resources\TankerDeliveries\Pages\ListTankerDeliveries;
use App\Filament\Staff\Resources\TankerDeliveries\Schemas\TankerDeliveryForm;
use App\Filament\Staff\Resources\TankerDeliveries\Tables\TankerDeliveriesTable;
use App\Models\TankerDelivery;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TankerDeliveryResource extends Resource
{
    protected static ?string $model = TankerDelivery::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Tanker Deliveries';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'tanker_plate_number';

    public static function form(Schema $schema): Schema
    {
        return TankerDeliveryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TankerDeliveriesTable::configure($table);
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
            'index' => ListTankerDeliveries::route('/'),
            'create' => CreateTankerDelivery::route('/create'),
            'edit' => EditTankerDelivery::route('/{record}/edit'),
        ];
    }
}
