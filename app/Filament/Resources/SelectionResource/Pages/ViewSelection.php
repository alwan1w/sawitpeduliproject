<?php

namespace App\Filament\Resources\SelectionResource\Pages;

use App\Filament\Resources\SelectionResource;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewSelection extends ViewRecord
{
    protected static string $resource = SelectionResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            // Tambahkan schema seperti di modal di atas
        ]);
    }
}
