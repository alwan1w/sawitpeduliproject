<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Recruitment;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\AgencyRecruitmentResource\Pages;

class AgencyRecruitmentResource extends Resource
{
    protected static ?string $model = Recruitment::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Agency';
    protected static ?string $navigationLabel = 'Rekrut Pekerja';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make('Informasi Rekrutmen')
                ->schema([
                    Forms\Components\TextInput::make('company.name')
                        ->label('Perusahaan')
                        ->disabled(),

                    Forms\Components\TextInput::make('position')
                        ->label('Posisi')
                        ->disabled(),

                    Forms\Components\Textarea::make('detail_posisi')
                        ->label('Detail Posisi')
                        ->disabled(),

                    Forms\Components\TextInput::make('requirement_total')
                        ->label('Jumlah Dibutuhkan')
                        ->disabled(),

                    Forms\Components\DatePicker::make('open_date')
                        ->label('Tanggal Dibuka')
                        ->disabled(),

                    Forms\Components\DatePicker::make('close_date')
                        ->label('Tanggal Ditutup')
                        ->disabled(),

                    Forms\Components\TextInput::make('salary_range')
                        ->label('Rentang Gaji')
                        ->disabled(),

                    Forms\Components\TextInput::make('contract_duration')
                        ->label('Durasi Kontrak')
                        ->disabled(),

                    Forms\Components\Textarea::make('skills')
                        ->label('Keterampilan')
                        ->disabled(),

                    Forms\Components\TextInput::make('age_range')
                        ->label('Rentang Usia')
                        ->disabled(),

                    Forms\Components\TextInput::make('education')
                        ->label('Pendidikan Minimal')
                        ->disabled(),
                ])
                ->columns(2),
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
                    ->columns(2)
                    ->required(),

                Forms\Components\Textarea::make('selection_process')
                    ->label('Proses Seleksi')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')->label('Perusahaan')->sortable(),
                TextColumn::make('position')->label('Posisi')->sortable(),
                TextColumn::make('requirement_total')->label('Kebutuhan'),

                TextColumn::make('applications_count')
                    ->label('Lamaran Masuk')
                    ->getStateUsing(fn ($record) => $record->applications()->where('status', 'masuk')->count()),

                TextColumn::make('seleksi_count')
                    ->label('Peserta Seleksi')
                    ->getStateUsing(fn ($record) => $record->applications()->where('status', 'seleksi')->count()),

                TextColumn::make('diterima_count')
                    ->label('Lolos Seleksi')
                    ->getStateUsing(fn ($record) => $record->applications()->whereIn('status', ['diterima', 'dikonfirmasi'])->count()),
            ])
            ->actions([
                Action::make('viewLamaranMasuk')
                    ->label('Lihat Lamaran Masuk')
                    ->url(fn ($record) => LamaranMasukResource::getUrl('index', ['tableFilters' => ['recruitment_id' => $record->id]]))
                    ->color('info'),
                Action::make('viewPesertaSeleksi')
                    ->label('Lihat Peserta Seleksi')
                    ->url(fn ($record) => SelectionResource::getUrl('index', ['tableFilters' => ['recruitment_id' => $record->id]]))
                    ->color('warning'),
                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgencyRecruitments::route('/'),
            // 'view'  => Pages\ViewAgencyRecruitment::route('/{record}'),
            'edit'  => Pages\EditAgencyRecruitment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('agency_id', Auth::id())
            ->where('status', 'mencari_pekerja');
    }
}
