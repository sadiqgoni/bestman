<?php

namespace App\Filament\Staff\Resources\TankerDeliveries\Pages;

use App\Filament\Staff\Resources\TankerDeliveries\TankerDeliveryResource;
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
