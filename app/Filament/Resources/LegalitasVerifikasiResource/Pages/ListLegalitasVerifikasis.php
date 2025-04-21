<?php

namespace App\Filament\Resources\LegalitasVerifikasiResource\Pages;

use App\Filament\Resources\LegalitasVerifikasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLegalitasVerifikasis extends ListRecords
{
    protected static string $resource = LegalitasVerifikasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
