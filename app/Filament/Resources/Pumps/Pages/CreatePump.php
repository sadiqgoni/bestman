<?php

namespace App\Filament\Resources\Pumps\Pages;

use App\Filament\Concerns\RedirectsToIndex;
use App\Filament\Resources\Pumps\PumpResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePump extends CreateRecord
{
    use RedirectsToIndex;

    protected static string $resource = PumpResource::class;
}
