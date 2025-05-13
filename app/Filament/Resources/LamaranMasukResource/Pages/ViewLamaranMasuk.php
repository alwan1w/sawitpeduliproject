<?php

namespace App\Filament\Resources\LamaranMasukResource\Pages;

use App\Filament\Resources\LamaranMasukResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewLamaranMasuk extends ViewRecord
{
    protected static string $resource = LamaranMasukResource::class;

    protected function getHeaderActions(): array
    {
        return [

            // Tombol Tolak
            Action::make('tolak')
                ->label('Tolak')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn () => $this->record->update([
                    'status' => 'ditolak',
                ]))
                ->after(function (): void {
                    // 1️⃣ Notification
                    Notification::make()
                        ->title('Lamaran berhasil ditolak')
                        ->success()
                        ->send();

                    // 2️⃣ Redirect ke index
                    $this->redirect(
                        $this->getResource()::getUrl('index')
                    );
                }),

            // Tombol Masuk Seleksi
            Action::make('seleksi')
                ->label('Masuk Seleksi')
                ->color('success')
                ->action(fn () => $this->record->update([
                    'status' => 'seleksi',
                ]))
                ->after(function (): void {
                    Notification::make()
                        ->title('Lamaran masuk tahap seleksi')
                        ->success()
                        ->send();

                    $this->redirect(
                        $this->getResource()::getUrl('index')
                    );
                }),
        ];
    }
}
