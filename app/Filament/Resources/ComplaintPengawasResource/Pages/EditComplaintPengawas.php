<?php

namespace App\Filament\Resources\ComplaintPengawasResource\Pages;

use App\Filament\Resources\ComplaintPengawasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComplaintPengawas extends EditRecord
{
    protected static string $resource = ComplaintPengawasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
