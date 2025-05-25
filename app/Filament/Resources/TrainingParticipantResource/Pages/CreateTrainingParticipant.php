<?php

namespace App\Filament\Resources\TrainingParticipantResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use App\Models\TrainingParticipant;
use App\Filament\Resources\TrainingParticipantResource;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;

class CreateTrainingParticipant extends CreateRecord
{
    protected static string $resource = TrainingParticipantResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $userId = Auth::id();
        $trainingId = $data['training_id'];

        // Cek apakah user sudah mendaftar ke pelatihan ini
        $exists = TrainingParticipant::where('user_id', $userId)
            ->where('training_id', $trainingId)
            ->exists();

        if ($exists) {
            // Tampilkan notifikasi error dan batalkan proses create
            Notification::make()
                ->title('Gagal Mendaftar')
                ->body('Kamu sudah pernah mendaftar ke pelatihan ini.')
                ->danger()
                ->send();

            // Throw untuk membatalkan proses simpan
            abort(403, 'Duplikat pendaftaran tidak diperbolehkan.');
        }

        return $data;
    }
}
