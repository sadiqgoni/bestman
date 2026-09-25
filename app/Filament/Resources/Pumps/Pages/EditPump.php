<?php

namespace App\Filament\Resources\Pumps\Pages;

use App\Filament\Concerns\RedirectsToIndex;
use App\Filament\Resources\Pumps\PumpResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPump extends EditRecord
{
    use RedirectsToIndex;

    protected static string $resource = PumpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
