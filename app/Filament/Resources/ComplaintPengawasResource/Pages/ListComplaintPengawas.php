<?php

namespace App\Filament\Resources\ComplaintPengawasResource\Pages;

use App\Filament\Resources\ComplaintPengawasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListComplaintPengawas extends ListRecords
{
    protected static string $resource = ComplaintPengawasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
