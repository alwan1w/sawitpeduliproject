<?php

namespace App\Filament\Resources\LegalitasVerifikasiResource\Pages;

use App\Filament\Resources\LegalitasVerifikasiResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLegalitasVerifikasi extends EditRecord
{
    protected static string $resource = LegalitasVerifikasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
