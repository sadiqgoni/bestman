<?php

namespace App\Filament\Resources\TankerDeliveries\Pages;

use App\Filament\Concerns\RedirectsToIndex;
use App\Filament\Resources\TankerDeliveries\TankerDeliveryResource;
use App\Models\TankerDelivery;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditTankerDelivery extends EditRecord
{
    use RedirectsToIndex;

    protected static string $resource = TankerDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->visible(fn (TankerDelivery $record): bool => $record->status === TankerDelivery::STATUS_PENDING),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->status !== TankerDelivery::STATUS_PENDING) {
            throw ValidationException::withMessages([
                'data.status' => 'Use the Confirm / Reject actions to review this delivery instead of editing it directly.',
            ]);
        }

        return $data;
    }
}
