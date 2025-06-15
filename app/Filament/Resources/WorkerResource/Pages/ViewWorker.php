<?php

namespace App\Filament\Resources\WorkerResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\WorkerResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\ImageEntry;

class ViewWorker extends ViewRecord
{
    protected static string $resource = WorkerResource::class;

    protected function getInfolistSchema(): array
    {
        return [
            Section::make('Data Worker')
                ->schema([
                    TextEntry::make('id')->label('ID Worker'),
                    TextEntry::make('application_id')->label('Application ID'),
                    TextEntry::make('application.profile_photo')->label('Profile Photo Value'),
                    ImageEntry::make('application.profile_photo')
                        ->label('Foto Profil')
                        ->circular()
                        ->height(80)
                        ->width(80)
                        ->defaultImageUrl('https://ui-avatars.com/api/?name=Fallback+Worker'),
                    TextEntry::make('application.name')->label('Nama Pekerja'),
                ])
        ];
    }
}

