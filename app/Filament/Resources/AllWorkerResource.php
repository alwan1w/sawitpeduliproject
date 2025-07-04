<?php

namespace App\Filament\Resources;

use App\Models\Worker;
use App\Models\Company;
use Filament\Tables\Table;
use App\Models\Recruitment;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Gate;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Resources\AllWorkerResource\Pages\ListAllWorkers;

class AllWorkerResource extends Resource
{
    protected static ?string $model = Worker::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Semua Pekerja';
    protected static ?string $navigationGroup = 'Pengawas';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('application.name')->label('Nama')->searchable(),
                TextColumn::make('application.recruitment.position')->label('Posisi')->searchable(),
                TextColumn::make('application.recruitment.company.name')->label('Perusahaan')->searchable(),
                TextColumn::make('start_date')->label('Mulai')->date('d M Y'),
                TextColumn::make('batas_kontrak')->label('Akhir Kontrak')->date('d M Y'),
                TextColumn::make('status_kontrak')->label('Status Kontrak'),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->label('Perusahaan')
                    ->options(fn () => Company::orderBy('name')->pluck('name', 'id')->toArray())
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        if (! $data['value']) return $query;
                        return $query->whereHas('application.recruitment', fn ($q) => $q->where('company_id', $data['value']));
                    }),

                SelectFilter::make('position')
                    ->label('Posisi')
                    ->options(fn () => Recruitment::orderBy('position')->pluck('position', 'position')->unique()->toArray())
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        if (! $data['value']) return $query;
                        return $query->whereHas('application.recruitment', fn ($q) => $q->where('position', $data['value']));
                    }),
            ])
            ->actions([
                // detail modal
                ViewAction::make(),
            ])
            ->defaultSort('start_date', 'desc');
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
            ]),
            Section::make('Sertifikasi yang Dimiliki')
            ->schema([
                TextEntry::make('sertifikasi_user')
                    ->label('')
                    ->html()
                    ->state(function ($record) {
                        // Ambil user_id dari application
                        $userId = $record->application?->user_id;
                        if (!$userId) return '<i>Data tidak tersedia</i>';

                        // Ambil nama sertifikasi "kompeten"
                        $sertifikasi = \App\Models\TrainingParticipant::where('user_id', $userId)
                            ->where('status', 'kompeten')
                            ->with('training.sertifikasi')
                            ->get()
                            ->pluck('training.sertifikasi.nama_sertifikasi')
                            ->filter()
                            ->unique()
                            ->values();

                        if ($sertifikasi->isEmpty()) {
                            return '<i>Tidak ada sertifikasi kompeten.</i>';
                        }

                        return '<ul style="padding-left:1em;margin:0;">' .
                            $sertifikasi->map(fn($s) => "<li>{$s}</li>")->implode('') .
                            '</ul>';
                    }),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAllWorkers::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        return Gate::allows('semua pekerja');
    }
}
