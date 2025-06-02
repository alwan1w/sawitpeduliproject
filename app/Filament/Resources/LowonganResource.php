<?php
namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables\Table;
use App\Models\Recruitment;
use App\Models\Sertifikasi;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\Action as TableAction;
use App\Filament\Resources\LowonganResource\Pages\ViewLowongan;
use App\Filament\Resources\LowonganResource\Pages\ListLowongans;

class LowonganResource extends Resource
{
    protected static ?string $model           = Recruitment::class;
    protected static ?string $navigationGroup = 'Pelamar';
    protected static ?string $navigationLabel = 'Lowongan Kerja';
    protected static ?string $navigationIcon  = 'heroicon-o-briefcase';
    // protected static string $slug              = 'lowongans'; // ganti URL segmennya

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')->label('Perusahaan')->sortable()->searchable(),
                TextColumn::make('position')->label('Posisi')->sortable()->searchable(),
                TextColumn::make('requirement_total')->label('Jumlah Dibutuhkan'),
                TextColumn::make('close_date')->label('Tutup')->date('d M Y'),
            ])
            ->actions([
                ViewAction::make(),
                TableAction::make('lamar')
                    ->label('Lamar')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->action(function ($record) {
                        $user = \App\Models\User::find(Auth::id()); // <--- PENTING, cast ke model asli

                        $requiredCerts = $record->required_certifications ?? [];

                        $userHasCerts = collect($requiredCerts)->every(function ($sertifikasiId) use ($user) {
                            return $user->trainings()
                                ->where('sertifikasi_id', $sertifikasiId)
                                ->wherePivot('status', 'kompeten')
                                ->exists();
                        });

                        if (!$userHasCerts) {
                            Notification::make()
                                ->title('Anda belum memenuhi syarat sertifikasi wajib.')
                                ->body('Silakan ikuti pelatihan yang relevan terlebih dahulu.')
                                ->danger()
                                ->send();
                            return;
                        }

                        return redirect(\App\Filament\Resources\ApplicationResource::getUrl('create', [
                            'recruitment_id' => $record->id,
                        ]));
                    })
                    ->visible(function (Recruitment $r) {
                        return !\App\Models\Application::where('user_id', Auth::id())
                            ->where('recruitment_id', $r->id)
                            ->exists();
                    }),



            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLowongans::route('/'),
            'view'  => ViewLowongan::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'mencari_pekerja')
            ->whereDate('close_date', '>=', today());
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            TextEntry::make('company.name')->label('Perusahaan'),
            TextEntry::make('position')->label('Posisi'),
            TextEntry::make('detail_posisi')->label('Detail Posisi'),
            TextEntry::make('salary_range')->label('Rentang Gaji'),
            TextEntry::make('contract_duration')->label('Durasi Kontrak'),
            TextEntry::make('skills')->label('Keahlian'),
            TextEntry::make('age_range')->label('Rentang Usia'),
            TextEntry::make('education')->label('Pendidikan Minimal'),

            TextEntry::make('required_documents')
                ->label('Dokumen yang Diperlukan')
                ->formatStateUsing(fn ($state): string =>
                    is_array($state) && count($state)
                        ? implode(', ', $state)
                        : (is_string($state) ? $state : '-')
                ),
           TextEntry::make('required_certifications')
                ->label('Sertifikasi Wajib')
                ->formatStateUsing(function ($state) {
                    // Jika state berupa string "1, 2" ubah jadi array
                    if (is_string($state)) {
                        $ids = array_map('trim', explode(',', $state));
                    } else {
                        $ids = $state;
                    }

                    // Pastikan tipe data integer
                    $ids = array_map('intval', $ids);

                    if (empty($ids)) return '-';

                    return \App\Models\Sertifikasi::whereIn('id', $ids)
                        ->pluck('nama_sertifikasi')
                        ->implode(', ');
                }),
            TextEntry::make('selection_process')->label('Proses Seleksi'),

            TextEntry::make('open_date')
                ->label('Tanggal Dibuka')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d M Y') : '—'),

            TextEntry::make('close_date')
                ->label('Tanggal Ditutup')
                ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d M Y') : '—'),
        ]);
    }

    public static function canAccess(): bool
    {
        return Gate::allows('akses lowongan');
    }
}
