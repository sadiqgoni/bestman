<?php

namespace App\Filament\Resources\DailyEntries\Pages;

use App\Filament\Concerns\RedirectsToIndex;
use App\Filament\Resources\DailyEntries\DailyEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDailyEntry extends EditRecord
{
    use RedirectsToIndex;

    protected static string $resource = DailyEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
