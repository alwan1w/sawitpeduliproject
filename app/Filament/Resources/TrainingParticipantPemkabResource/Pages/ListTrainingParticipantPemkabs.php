<?php

namespace App\Filament\Resources\TrainingParticipantPemkabResource\Pages;

use App\Filament\Resources\TrainingParticipantPemkabResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTrainingParticipantPemkabs extends ListRecords
{
    protected static string $resource = TrainingParticipantPemkabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
