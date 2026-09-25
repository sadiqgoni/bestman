<?php

namespace App\Filament\Staff\Resources\TankerDeliveries\Pages;

use App\Filament\Concerns\RedirectsToIndex;
use App\Filament\Staff\Resources\TankerDeliveries\TankerDeliveryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTankerDelivery extends CreateRecord
{
    use RedirectsToIndex;

    protected static string $resource = TankerDeliveryResource::class;
}
