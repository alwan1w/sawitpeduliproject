<?php

namespace App\Filament\Resources;

use App\Models\Worker;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Application;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\WorkerResource\Pages;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;
    protected static ?string $navigationGroup = 'Agency';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Kelola Pekerja';

    public static function form(Form $form): Form
    {
        // Tidak pakai form input (read-only table)
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('company.name')
                ->label('Perusahaan'),

            TextColumn::make('application.name')
                ->label('Nama Pekerja')
                ->searchable(),

            TextColumn::make('application.recruitment.position')
                ->label('Posisi'),

            TextColumn::make('application.phone')
                ->label('Telepon')
                ->searchable(),

            TextColumn::make('end_date')
                ->label('Batas Kontrak')
                ->date('d F Y'),

        ])
        ->defaultSort('start_date', 'desc')
        ->actions([
            ViewAction::make(),

            Action::make('hapus')
                ->label('Hapus')
                ->requiresConfirmation()
                ->color('danger')
                ->icon('heroicon-o-trash')
                ->action(function (Worker $record) {
                    DB::transaction(function () use ($record) {
                        $record->load(['user', 'application']);

                        if ($record->application) {
                            $record->application->update(['status' => 'ditolak']);
                        }

                        if ($record->user) {
                            $record->user->syncRoles(['pelamar']);
                        }

                        $record->delete();
                    });

                    Notification::make()
                        ->title('Pekerja dihapus dan role diubah menjadi pelamar.')
                        ->success()
                        ->send();
                }),


        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->whereHas('application', function (Builder $query) {
                $query->where('status', 'Dikonfirmasi');
            });

        // Debugging: Log hasil query
        Log::debug('WorkerResource Query Result:', [
            'workers' => $query->get()->toArray(),
            'total' => $query->count(),
        ]);

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkers::route('/'),
            // 'view' => Pages\ViewWorker::route('/{record}'), // Tambahkan halaman view
        ];
    }

    public static function canAccess(): bool
    {
        return Gate::allows('akses kelola pekerja agen');
    }
}
