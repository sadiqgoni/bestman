<?php

namespace App\Filament\Resources\DailyEntries\Pages;

use App\Filament\Concerns\RedirectsToIndex;
use App\Filament\Resources\DailyEntries\DailyEntryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDailyEntry extends CreateRecord
{
    use RedirectsToIndex;

    protected static string $resource = DailyEntryResource::class;
}
