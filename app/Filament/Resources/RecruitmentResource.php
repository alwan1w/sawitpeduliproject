<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Recruitment;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\RecruitmentResource\Pages;
use Illuminate\Support\Facades\Auth;

class RecruitmentResource extends Resource
{
    protected static ?string $model = Recruitment::class;
    protected static ?string $navigationLabel = 'Rekrutmen';
    protected static ?string $navigationGroup = 'Perusahaan';
    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Select::make('company_id')
                ->relationship('company', 'name')
                ->label('Perusahaan')
                ->required(),

            Select::make('agency_id')
                ->label('Agen Tujuan')
                ->options(self::getAgencyOptions())
                ->searchable()
                ->required(),

            TextInput::make('position')->label('Posisi')->required(),
            Textarea::make('detail_posisi')->label('Detail Posisi')->rows(3)->columnSpanFull(),
            TextInput::make('requirement_total')->label('Jumlah Kebutuhan')->numeric()->required(),
            DatePicker::make('open_date')->label('Tanggal Mulai')->required(),
            DatePicker::make('close_date')->label('Tanggal Tutup')->required(),
            TextInput::make('salary_range')->label('Rentang Gaji'),
            TextInput::make('contract_duration')->label('Durasi Kontrak'),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecruitments::route('/'),
            // 'view'  => Pages\ViewRecruitment::route('/{record}'),
            'create'=> Pages\CreateRecruitment::route('/create'),
            'edit'  => Pages\EditRecruitment::route('/{record}/edit'),
        ];
    }

    // public static function getEloquentQuery(): Builder
    // {
    //     // owner only sees miliknya
    //     return parent::getEloquentQuery()
    //         ->where('company_id', Auth::id());
    // }

    protected static function getAgencyOptions()
    {
        return User::role('agen')->pluck('name', 'id');
    }
}
