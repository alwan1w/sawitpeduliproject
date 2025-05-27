<?php

namespace App\Filament\Resources\ComplaintAdminResource\Pages;

use App\Filament\Resources\ComplaintAdminResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComplaintAdmin extends EditRecord
{
    protected static string $resource = ComplaintAdminResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
