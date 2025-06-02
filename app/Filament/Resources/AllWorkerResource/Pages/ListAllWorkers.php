<?php

namespace App\Filament\Resources\AllWorkerResource\Pages;

use App\Filament\Resources\AllWorkerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAllWorkers extends ListRecords
{
    protected static string $resource = AllWorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
