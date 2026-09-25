<?php

namespace App\Filament\Resources\Tanks\Pages;

use App\Filament\Concerns\RedirectsToIndex;
use App\Filament\Resources\Tanks\TankResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTank extends CreateRecord
{
    use RedirectsToIndex;

    protected static string $resource = TankResource::class;
}
