<?php

namespace App\Filament\Resources\CompanyWorkerResource\Pages;

use App\Filament\Resources\CompanyWorkerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyWorker extends EditRecord
{
    protected static string $resource = CompanyWorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
