<?php

namespace App\Filament\Resources;

use App\Models\Worker;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Application;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\WorkerResource\Pages;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;
    protected static ?string $navigationGroup = 'Agency';
    protected static ?int $navigationSort = 4;
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

            TextColumn::make('batas_kontrak')
                ->label('Batas Kontrak')
                ->date('d F Y')
                ->sortable(),

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

     // INILAH KUNCI: InfoList detail View Worker
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            ImageEntry::make('application.profile_photo')
                ->label('Foto Profil')
                ->circular()
                ->height(80)
                ->width(80)
                ->columnSpanFull()
                ->defaultImageUrl('https://ui-avatars.com/api/?name=Fallback+Worker'),

            Section::make('Data Pekerja')
            ->columns(2) // opsional, kalau ingin dua kolom
            ->schema([
                TextEntry::make('company.name')->label('Perusahaan'),
                TextEntry::make('application.name')->label('Nama Pekerja'),
                TextEntry::make('application.recruitment.position')->label('Posisi'),
                TextEntry::make('application.phone')->label('Telepon'),
                TextEntry::make('start_date')->label('Mulai')->date('d M Y'),
                TextEntry::make('batas_kontrak')->label('Batas Kontrak')->date('d M Y'),
            ])
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['application.recruitment', 'application.user', 'company'])
            ->whereHas('application', fn ($q) => $q->where('status', 'Dikonfirmasi'))
            ->whereHas('application.recruitment', fn ($q) => $q->where('agency_id', Auth::id()));

    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkers::route('/'),
            // 'view' => Pages\ViewWorker::route('/{record}'),

        ];
    }

    public static function canAccess(): bool
    {
        return Gate::allows('akses kelola pekerja agen');
    }
}
