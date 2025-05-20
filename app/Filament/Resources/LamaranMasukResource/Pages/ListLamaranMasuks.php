<?php

namespace App\Filament\Resources\LamaranMasukResource\Pages;

use App\Filament\Resources\LamaranMasukResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLamaranMasuks extends ListRecords
{
    protected static string $resource = LamaranMasukResource::class;

    protected function getHeaderActions(): array
    {
        return []; // Kosongkan agar tombol "Create" tidak muncul
    }
}
