<?php

namespace App\Filament\Resources\TrainingParticipantResource\Pages;

use App\Filament\Resources\TrainingParticipantResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrainingParticipant extends EditRecord
{
    protected static string $resource = TrainingParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
