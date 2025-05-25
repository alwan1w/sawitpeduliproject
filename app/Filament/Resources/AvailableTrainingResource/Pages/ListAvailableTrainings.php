<?php

namespace App\Filament\Resources\AvailableTrainingResource\Pages;

use App\Filament\Resources\AvailableTrainingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAvailableTrainings extends ListRecords
{
    protected static string $resource = AvailableTrainingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
