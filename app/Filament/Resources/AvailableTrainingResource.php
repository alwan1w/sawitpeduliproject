<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Training;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\AvailableTrainingResource\Pages\ListAvailableTrainings;

class AvailableTrainingResource extends Resource
{
    protected static ?string $model = Training::class;
    protected static ?string $navigationGroup = 'Pelamar';
    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Daftar Pelatihan';

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tema_pelatihan')->label('Tema'),
                TextColumn::make('tanggal_pelatihan')->label('Tanggal')->date(),
                TextColumn::make('kuota_peserta')->label('Kuota'),
            ])
            ->actions([
                Action::make('daftar')
                    ->label('Daftar')
                    ->action(function ($record) {
                        $userId = Auth::id();
                        $trainingId = $record->id;

                        $sudahDaftar = \App\Models\TrainingParticipant::where('user_id', $userId)
                            ->where('training_id', $trainingId)
                            ->exists();

                        if ($sudahDaftar) {
                            \Filament\Notifications\Notification::make()
                                ->title('Kamu sudah mendaftar!')
                                ->body('Kamu hanya bisa mendaftar satu kali untuk pelatihan ini.')
                                ->danger()
                                ->send();

                            return;
                        }

                        // Redirect manual ke halaman create dengan training_id
                        return redirect()->to(\App\Filament\Resources\TrainingParticipantResource::getUrl('create', ['training_id' => $trainingId]));
                    })
                    // ->requiresConfirmation() // opsional kalau mau ada konfirmasi klik
                    ->visible(fn($record) =>
                        $record->tanggal_pelatihan >= now() &&
                        $record->participants()->count() < $record->kuota_peserta
                    )

            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        // hanya tampilkan pelatihan yg masih available
        return parent::getEloquentQuery()
            ->where('tanggal_pelatihan', '>=', now())
            ->whereRaw('(SELECT COUNT(*) FROM training_participants WHERE training_id = trainings.id) < kuota_peserta');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAvailableTrainings::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        return Gate::allows('daftar pelatihan');
    }
}
