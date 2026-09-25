<?php

namespace App\Filament\Staff\Resources\DailyEntries\Pages;

use App\Filament\Staff\Resources\DailyEntries\DailyEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDailyEntries extends ListRecords
{
    protected static string $resource = DailyEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
