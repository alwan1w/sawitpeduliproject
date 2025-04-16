<?php

namespace App\Filament\Resources\KerjasamaResource\Pages;

use App\Filament\Resources\KerjasamaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKerjasama extends EditRecord
{
    protected static string $resource = KerjasamaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
