<?php

namespace App\Filament\Resources\TankerDeliveries\Pages;

use App\Filament\Concerns\RedirectsToIndex;
use App\Filament\Resources\TankerDeliveries\TankerDeliveryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTankerDelivery extends CreateRecord
{
    use RedirectsToIndex;

    protected static string $resource = TankerDeliveryResource::class;
}
