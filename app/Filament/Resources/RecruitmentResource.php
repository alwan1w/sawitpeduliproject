<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Recruitment;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RecruitmentResource\Pages;

class RecruitmentResource extends Resource
{
    protected static ?string $model = Recruitment::class;
    protected static ?string $navigationLabel = 'Rekrutmen';
    protected static ?string $navigationGroup = 'Perusahaan';
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form->schema([
            \Filament\Forms\Components\Hidden::make('company_id')
            ->default(fn () => \App\Models\Company::where('id', Auth::id())->value('id'))
            ->required(),

            Select::make('agency_id')
                ->label('Agen Tujuan')
                ->options(self::getAgencyOptions())
                ->searchable()
                ->required(),

            Select::make('required_certifications')
                ->label('Sertifikasi Wajib')
                ->multiple() // untuk multi-pilih
                ->options(\App\Models\Sertifikasi::pluck('nama_sertifikasi', 'id')) // ambil dari tabel sertifikasis
                ->searchable()
                ->helperText('Pilih satu atau lebih sertifikasi yang wajib dimiliki.'),

            TextInput::make('position')->label('Posisi')->required(),
            Textarea::make('detail_posisi')->label('Detail Posisi')->rows(3)->columnSpanFull(),
            TextInput::make('requirement_total')->label('Jumlah Kebutuhan')->numeric()->required(),
            DatePicker::make('open_date')->label('Tanggal Mulai')->required(),
            DatePicker::make('close_date')->label('Tanggal Tutup')->required(),
            TextInput::make('salary_range')->label('Rentang Gaji'),
            TextInput::make('contract_duration')
                ->label('Durasi Kontrak (bulan)')
                ->numeric()
                ->minValue(1)
                ->required(),
            Textarea::make('skills')->label('Keahlian Diperlukan'),
            TextInput::make('age_range')->label('Rentang Usia'),
            TextInput::make('education')->label('Pendidikan Minimal'),

            TextInput::make('status')
                ->label('Status Rekrutmen')
                ->default('mencari_agen')
                ->disabled(),

            TextInput::make('agency_status')
                ->label('Status Agen')
                ->default('menunggu')
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')->label('Perusahaan'),
                TextColumn::make('position')->label('Posisi')->sortable()->searchable(),
                TextColumn::make('agency.name')->label('Agen Tujuan')->default('-'),
                TextColumn::make('requirement_total')->label('Kebutuhan'),
                TextColumn::make('close_date')
                    ->label('Tutup')
                    ->date('d M Y'),
                TextColumn::make('required_certifications')
                    ->label('Sertifikasi Wajib')
                    ->formatStateUsing(fn ($state) =>
                        $state
                            ? \App\Models\Sertifikasi::whereIn('id', (array)$state)->pluck('nama_sertifikasi')->implode(', ')
                            : '-'
                    ),
                TextColumn::make('status')
                    ->label('Status Rekrutmen')
                    ->formatStateUsing(fn($state) => match($state) {
                        'mencari_agen'    => 'Mencari Agen',
                        'mencari_pekerja' => 'Mencari Pekerja',
                        'selesai'         => 'Selesai',
                        default           => $state,
                    })
                    ->badge(),
                TextColumn::make('agency_status')
                    ->label('Status Agen')
                    ->formatStateUsing(fn($state, $record) => match($state) {
                        'menunggu' => "Menunggu ({$record->agency?->name})",
                        'diterima' => "Diterima ({$record->agency?->name})",
                        'ditolak'  => "Ditolak ({$record->agency?->name})",
                        default    => $state,
                    })
                    ->badge(),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('Lempar ke Agen Lain')
                ->label('Lempar ke Agen Lain')
                ->color('warning')
                ->form([
                    Select::make('agency_id')
                        ->label('Pilih Agen Baru')
                        ->options(self::getAgencyOptions())
                        ->searchable()
                        ->required(),
                ])
                ->visible(fn ($record) => $record->agency_status === 'ditolak' || is_null($record->agency_status))
                ->action(function ($record, array $data) {
                    $record->update([
                        'agency_id' => $data['agency_id'],
                        'agency_status' => 'menunggu',
                    ]);
                }),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Info Rekrutmen')
                ->schema([
                    TextEntry::make('company.name')->label('Perusahaan'),
                    TextEntry::make('position')->label('Posisi'),
                    TextEntry::make('detail_posisi')->label('Detail Posisi'),
                    TextEntry::make('requirement_total')->label('Jumlah Kebutuhan'),
                    TextEntry::make('open_date')
                        ->label('Tanggal Mulai')
                        ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d M Y') : '-'),
                    TextEntry::make('close_date')
                        ->label('Tanggal Tutup')
                        ->formatStateUsing(fn ($state) => $state ? \Carbon\Carbon::parse($state)->format('d M Y') : '-'),
                    TextEntry::make('salary_range')->label('Rentang Gaji'),
                    TextEntry::make('contract_duration')->label('Durasi Kontrak (bulan)'),
                    TextEntry::make('skills')->label('Keahlian Diperlukan'),
                    TextEntry::make('age_range')->label('Rentang Usia'),
                    TextEntry::make('education')->label('Pendidikan Minimal'),
                ])
                ->columns(2),

            Section::make('Agen dan Status')
                ->schema([
                    TextEntry::make('agency.name')->label('Agen Tujuan')->default('-'),
                    TextEntry::make('status')
                        ->label('Status Rekrutmen')
                        ->formatStateUsing(fn($state) => match($state) {
                            'mencari_agen'    => 'Mencari Agen',
                            'mencari_pekerja' => 'Mencari Pekerja',
                            'selesai'         => 'Selesai',
                            default           => $state,
                        }),
                    TextEntry::make('agency_status')
                        ->label('Status Agen')
                        ->formatStateUsing(fn($state, $record) => match($state) {
                            'menunggu' => "Menunggu ({$record->agency?->name})",
                            'diterima' => "Diterima ({$record->agency?->name})",
                            'ditolak'  => "Ditolak ({$record->agency?->name})",
                            default    => $state,
                        }),
                ])
                ->columns(2),

            Section::make('Sertifikasi Wajib')
                ->schema([
                    TextEntry::make('required_certifications')
                        ->label('Sertifikasi Wajib')
                        ->html()
                        ->state(function ($record) {
                            $ids = is_array($record->required_certifications)
                                ? $record->required_certifications
                                : (is_string($record->required_certifications)
                                    ? array_map('intval', explode(',', $record->required_certifications))
                                    : []);
                            if (empty($ids)) return '<i>- Tidak ada sertifikasi wajib -</i>';
                            $names = \App\Models\Sertifikasi::whereIn('id', $ids)
                                ->pluck('nama_sertifikasi')
                                ->toArray();
                            return '<ul style="padding-left:1em;">'
                                . collect($names)->map(fn($n) => "<li>{$n}</li>")->implode('')
                                . '</ul>';
                        }),
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecruitments::route('/'),
            // 'view'  => Pages\ViewRecruitment::route('/{record}'),
            'create'=> Pages\CreateRecruitment::route('/create'),
            'edit'  => Pages\EditRecruitment::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // owner only sees miliknya
        return parent::getEloquentQuery()
            ->where('company_id', Auth::id());
    }

    protected static function getAgencyOptions()
    {
        // hanya tampilkan agency dengan legalitas 'terverifikasi'
        return User::role('agen')
            ->whereHas('legalitas', fn($q) => $q->where('status', 'terverifikasi'))
            ->pluck('name', 'id');
    }

    public static function canAccess(): bool
    {
        return Gate::allows('akses rekrutment');
    }
}
