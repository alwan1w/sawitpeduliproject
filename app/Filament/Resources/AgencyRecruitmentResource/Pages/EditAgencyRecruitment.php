<?php

namespace App\Filament\Resources\AgencyRecruitmentResource\Pages;

use App\Filament\Resources\AgencyRecruitmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAgencyRecruitment extends EditRecord
{
    protected static string $resource = AgencyRecruitmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
