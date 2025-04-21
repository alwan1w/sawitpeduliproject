<?php

namespace App\Filament\Resources\LegalitasResource\Pages;

use App\Filament\Resources\LegalitasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLegalitas extends EditRecord
{
    protected static string $resource = LegalitasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
