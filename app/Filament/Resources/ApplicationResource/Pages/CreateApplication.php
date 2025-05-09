<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateApplication extends CreateRecord
{
    protected static string $resource = ApplicationResource::class;

    // redirect ke index setelah sukses созда
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
