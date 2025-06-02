<?php

namespace App\Filament\Resources\AllWorkerResource\Pages;

use App\Filament\Resources\AllWorkerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAllWorker extends EditRecord
{
    protected static string $resource = AllWorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
