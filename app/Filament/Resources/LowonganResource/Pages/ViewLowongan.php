<?php
namespace App\Filament\Resources\LowonganResource\Pages;

use App\Filament\Resources\ApplicationResource;
use App\Filament\Resources\LowonganResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;

class ViewLowongan extends ViewRecord
{
    protected static string $resource = LowonganResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('lamar')
                ->label('Lamar Pekerjaan')
                ->icon('heroicon-o-document-text')
                ->url(fn (): string => ApplicationResource::getUrl('create', [
                    'record' => $this->record->id,
                ]))
                ->openUrlInNewTab()
                ->color('primary'),
        ];
    }
}
