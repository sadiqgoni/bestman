<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile')
                    ->description('Basic identity for this account.')
                    ->icon('heroicon-o-user')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                    ]),

                Section::make('Access & security')
                    ->description('Admin accounts can access the operations dashboard; Staff accounts can only use the Staff Portal.')
                    ->icon('heroicon-o-shield-check')
                    ->columns(2)
                    ->schema([
                        Select::make('role')
                            ->options([
                                'ADMIN' => 'Admin',
                                'STAFF' => 'Staff',
                            ])
                            ->required()
                            ->default('STAFF'),
                        TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->label(fn (string $operation): string => $operation === 'edit' ? 'New password' : 'Password')
                            ->helperText(fn (string $operation): ?string => $operation === 'edit' ? 'Leave blank to keep the current password.' : null)
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(fn (?string $state): bool => filled($state)),
                    ]),
            ]);
    }
}
