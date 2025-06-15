<?php

namespace App\Filament\Resources;

use App\Models\Worker;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\CompanyWorkerResource\Pages;

class CompanyWorkerResource extends Resource
{
    protected static ?string $model = Worker::class;
    protected static ?string $navigationGroup = 'Perusahaan';
    protected static ?string $navigationLabel = 'Kelola Pekerja';
    protected static ?string $navigationIcon  = 'heroicon-o-user-group';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('application.name')->label('Nama Pekerja') ->searchable(),
                TextColumn::make('application.recruitment.agency.name')->label('Agen')->searchable(),
                TextColumn::make('batas_kontrak')->label('Batas Kontrak')->date('d M Y'),
                TextColumn::make('application.recruitment.position')
                    ->label('Posisi'),

                TextColumn::make('application.phone')
                    ->label('Telepon')
                    ->searchable(),
                TextColumn::make('status_kontrak')->label('Status')->searchable(),
            ])
            ->actions([
                // detail modal
                ViewAction::make(),

                // Putus Kontrak
                Action::make('putus')
                    ->label('PUTUS')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            $record->update(['status_kontrak' => 'putus']);

                            if ($record->application) {
                                $record->application->update(['status' => 'ditolak']);
                            }

                            if ($record->user) {
                                $record->user->syncRoles(['pelamar']);
                            }

                            $record->delete();
                        });

                        Notification::make()
                            ->title('Kontrak pekerja telah diputus. Role dikembalikan menjadi pelamar.')
                            ->success()
                            ->send();
                    }),

                    // ->visible(fn ($record) => $record->status_kontrak !== 'putus'),


                // Perpanjang Kontrak
                Action::make('perpanjang')
                    ->label('PERPANJANG')
                    ->color('success')
                    ->form([
                        DatePicker::make('batas_kontrak')->label('Perpanjang Sampai')->required(),
                    ])
                    ->action(fn ($record, array $data) =>
                        $record->update([
                            'batas_kontrak' => $data['batas_kontrak'],
                            'status_kontrak' => 'aktif',
                        ])
                    )
                    // ->visible(fn ($record) => $record->status_kontrak !== 'putus'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanyWorkers::route('/'),

        ];
    }

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
        // hanya pekerja untuk perusahaan login
        return parent::getEloquentQuery()
            ->where('company_id', Auth::id());
    }

    public static function canAccess(): bool
    {
        return Gate::allows('akses kelola pekerja perusahaan');
    }
}
