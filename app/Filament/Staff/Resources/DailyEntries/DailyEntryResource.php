<?php

namespace App\Filament\Staff\Resources\DailyEntries;

use App\Filament\Staff\Resources\DailyEntries\Pages\CreateDailyEntry;
use App\Filament\Staff\Resources\DailyEntries\Pages\EditDailyEntry;
use App\Filament\Staff\Resources\DailyEntries\Pages\ListDailyEntries;
use App\Filament\Staff\Resources\DailyEntries\Schemas\DailyEntryForm;
use App\Filament\Staff\Resources\DailyEntries\Tables\DailyEntriesTable;
use App\Models\DailyEntry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DailyEntryResource extends Resource
{
    protected static ?string $model = DailyEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|\UnitEnum|null $navigationGroup = 'Operations';

    protected static ?string $navigationLabel = 'Daily Entries';

    protected static ?int $navigationSort = 20;

    protected static ?string $recordTitleAttribute = 'entry_date';

    public static function form(Schema $schema): Schema
    {
        return DailyEntryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DailyEntriesTable::configure($table);
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
            'index' => ListDailyEntries::route('/'),
            'create' => CreateDailyEntry::route('/create'),
            'edit' => EditDailyEntry::route('/{record}/edit'),
        ];
    }
}
