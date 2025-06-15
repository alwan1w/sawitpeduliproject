<?php

namespace App\Filament\Resources\AgencyRecruitmentResource\Pages;

use App\Filament\Resources\AgencyRecruitmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAgencyRecruitments extends ListRecords
{
    protected static string $resource = AgencyRecruitmentResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
