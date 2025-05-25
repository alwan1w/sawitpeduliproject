<?php

namespace App\Filament\Resources\TrainingParticipantResource\Pages;

use App\Filament\Resources\TrainingParticipantResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrainingParticipants extends ListRecords
{
    protected static string $resource = TrainingParticipantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
