<?php
namespace App\Filament\Resources\LowonganResource\Pages;

use App\Models\User;
use App\Models\Application;
use Filament\Actions\Action;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\LowonganResource;
use App\Filament\Resources\ApplicationResource;

class ViewLowongan extends ViewRecord
{
    protected static string $resource = LowonganResource::class;

    protected function getHeaderActions(): array
    {
        $user = User::find(Auth::id()); // Cast ke model User agar relasi jalan
        $record = $this->record;

        $requiredCerts = $record->required_certifications ?? [];

        // Cek apakah sudah pernah melamar
        $sudahMelamar = Application::where('user_id', $user->id)
            ->where('recruitment_id', $record->id)
            ->exists();

        // Cek sertifikasi yang dimiliki
        $userHasCerts = collect($requiredCerts)->every(function ($sertifikasiId) use ($user) {
            return $user->trainings()
                ->where('sertifikasi_id', $sertifikasiId)
                ->wherePivot('status', 'kompeten')
                ->exists();
        });

        return [
            Action::make('lamar')
                ->label('Lamar Pekerjaan')
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->visible(function () use ($sudahMelamar, $userHasCerts) {
                    // Hanya tampil jika BELUM melamar
                    return !$sudahMelamar;
                })
                ->action(function () use ($userHasCerts, $record) {
                    if (!$userHasCerts) {
                        Notification::make()
                            ->title('Tidak Memenuhi Syarat Sertifikasi')
                            ->body('Anda belum memiliki seluruh sertifikasi wajib. Silakan ikuti pelatihan yang relevan dahulu.')
                            ->danger()
                            ->send();
                        return;
                    }
                    // Redirect ke form lamaran
                    return redirect(ApplicationResource::getUrl('create', [
                        'recruitment_id' => $record->id,
                    ]));
                }),
        ];
    }
}
