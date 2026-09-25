<?php

namespace App\Filament\Concerns;

trait RedirectsToIndex
{
    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
