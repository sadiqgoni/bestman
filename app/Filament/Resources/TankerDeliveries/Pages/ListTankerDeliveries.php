<?php

namespace App\Filament\Resources\TankerDeliveries\Pages;

use App\Filament\Resources\TankerDeliveries\TankerDeliveryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTankerDeliveries extends ListRecords
{
    protected static string $resource = TankerDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
