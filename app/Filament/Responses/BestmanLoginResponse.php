<?php

namespace App\Filament\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class BestmanLoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        if (Filament::getCurrentOrDefaultPanel()->getId() === 'staff') {
            return redirect('/staff/daily-entries');
        }

        return redirect()->intended(Filament::getUrl());
    }
}
