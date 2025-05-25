<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Recruitment;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\KerjasamaResource\Pages;

class KerjasamaResource extends Resource
{
    protected static ?string $model = Recruitment::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Agency';
    protected static ?string $navigationLabel = 'Kerjasama';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('position')->label('Posisi')->disabled(),
            Forms\Components\Textarea::make('detail_posisi')->label('Detail Posisi')->disabled(),
            Forms\Components\TextInput::make('requirement_total')->label('Jumlah Dibutuhkan')->disabled(),
            Forms\Components\DatePicker::make('open_date')->label('Tanggal Dibuka')->disabled(),
            Forms\Components\DatePicker::make('close_date')->label('Tanggal Ditutup')->disabled(),
            Forms\Components\TextInput::make('salary_range')->label('Gaji')->disabled(),
            Forms\Components\TextInput::make('contract_duration')->label('Durasi Kontrak')->disabled(),
            Forms\Components\Textarea::make('skills')->label('Keterampilan')->disabled(),
            Forms\Components\TextInput::make('age_range')->label('Usia')->disabled(),
            Forms\Components\TextInput::make('education')->label('Pendidikan')->disabled(),
            Forms\Components\TextInput::make('status')->label('Status')->disabled(),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            TextColumn::make('company.name')->label('Perusahaan'),
            TextColumn::make('position')->label('Permintaan Posisi'),
            TextColumn::make('requirement_total')->label('Jumlah Kebutuhan'),
            TextColumn::make('close_date')->label('Batas Waktu')->date('d M Y'),
        ])
        ->actions([
            ViewAction::make(),

            Action::make('Terima')
                ->label('Terima Permintaan')
                ->color('success')
                ->form([
                    Forms\Components\CheckboxList::make('required_documents')
                        ->label('Dokumen yang Diperlukan')
                        ->options([
                            'Curriculum Vitae' => 'Curriculum Vitae',
                            'Sertifikat Pelatihan' => 'Sertifikat Pelatihan',
                            'Fotokopi Ijazah' => 'Fotokopi Ijazah',
                            'SK Sehat' => 'SK Sehat',
                            'Pas Foto 3x4' => 'Pas Foto 3x4',
                            'Fotokopi KTP' => 'Fotokopi KTP',
                            'Fotokopi KK' => 'Fotokopi KK',
                            'SKCK' => 'SKCK',
                            'Transkrip Nilai' => 'Transkrip Nilai',
                            'SIM A/C/B/B2' => 'SIM A/C/B/B2',
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('selection_process')
                        ->label('Proses Seleksi')
                        ->maxLength(500)
                        ->required(),
                ])
                ->action(function (Recruitment $record, array $data) {
                    $record->update([
                        'agency_id' => Auth::id(),
                        'status' => 'mencari_pekerja',
                        'agency_status' => 'diterima',
                        'required_documents' => $data['required_documents'],
                        'selection_process' => $data['selection_process'],
                    ]);
                })
                ->visible(fn ($record) =>
                    $record->status === 'mencari_agen' &&
                    ($record->agency_id === null || $record->agency_id === Auth::id()) &&
                    ($record->agency_status === null || $record->agency_status === 'menunggu')
                ),

            Action::make('Tolak')
                ->label('Tolak Permintaan')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (Recruitment $record) {
                    $record->update([
                        'agency_id' => Auth::id(),
                        'agency_status' => 'ditolak',
                    ]);
                })
                ->visible(fn ($record) =>
                    $record->status === 'mencari_agen' &&
                    ($record->agency_id === null || $record->agency_id === Auth::id()) &&
                    ($record->agency_status === null || $record->agency_status === 'menunggu')
                ),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKerjasamas::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'mencari_agen')
            ->where(function ($query) {
                $query->whereNull('agency_id')
                      ->orWhere('agency_id', Auth::id());
            });
    }

    public static function canAccess(): bool
    {
        return Gate::allows('akses kerjasama');
    }
}
