<?php

namespace App\Filament\Auth\Pages;

use App\Models\User;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;

class Register extends BaseRegister
{
    protected function handleRegistration(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // Tambahkan role "pelamar"
        $user->assignRole('pelamar');

        return $user;
    }
}
