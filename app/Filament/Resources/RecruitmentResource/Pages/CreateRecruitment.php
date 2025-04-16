<?php

namespace App\Filament\Resources\RecruitmentResource\Pages;

use App\Models\Recruitment;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\RecruitmentResource;
use Illuminate\Support\Facades\Auth;

class CreateRecruitment extends CreateRecord
{
    protected static string $resource = RecruitmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = Auth::id();
        $data['status'] = 'mencari_agen';
        return $data;
    }
}
