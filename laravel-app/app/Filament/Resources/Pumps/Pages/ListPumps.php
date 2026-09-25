<?php

namespace App\Filament\Resources\Pumps\Pages;

use App\Filament\Resources\Pumps\PumpResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPumps extends ListRecords
{
    protected static string $resource = PumpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
