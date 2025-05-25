<?php

namespace App\Filament\Resources\AvailableTrainingResource\Pages;

use App\Filament\Resources\AvailableTrainingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAvailableTraining extends EditRecord
{
    protected static string $resource = AvailableTrainingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
