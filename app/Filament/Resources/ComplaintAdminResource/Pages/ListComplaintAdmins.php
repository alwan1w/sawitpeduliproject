<?php

namespace App\Filament\Resources\ComplaintAdminResource\Pages;

use App\Filament\Resources\ComplaintAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListComplaintAdmins extends ListRecords
{
    protected static string $resource = ComplaintAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
