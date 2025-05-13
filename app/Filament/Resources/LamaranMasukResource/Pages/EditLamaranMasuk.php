<?php

namespace App\Filament\Resources\LamaranMasukResource\Pages;

use App\Filament\Resources\LamaranMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLamaranMasuk extends EditRecord
{
    protected static string $resource = LamaranMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
