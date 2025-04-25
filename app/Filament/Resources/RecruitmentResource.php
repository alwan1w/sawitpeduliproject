<?php

namespace App\Filament\Resources;

use App\Models\Recruitment;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Resources\RecruitmentResource\Pages;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\Action;

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

            TextInput::make('position')->label('Posisi')->required(),
            Textarea::make('detail_posisi')->label('Detail Posisi')->rows(3)->columnSpanFull(),

            TextInput::make('requirement_total')->label('Kebutuhan')->numeric()->required(),
            DatePicker::make('open_date')->label('Tanggal Dibuka')->required(),
            DatePicker::make('close_date')->label('Tanggal Ditutup')->required(),

            TextInput::make('salary_range')->label('Rentang Gaji'),
            TextInput::make('contract_duration')->label('Durasi Kontrak'),
            Textarea::make('skills')->label('Keahlian'),
            TextInput::make('age_range')->label('Rentang Usia'),
            TextInput::make('education')->label('Pendidikan Minimal'),

            TextInput::make('status')
                ->label('Status')
                ->default('mencari_agen')
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('position')->label('Posisi')->sortable()->searchable(),
            TextColumn::make('detail_posisi')->label('Detail Posisi')->limit(40)->wrap(),
            TextColumn::make('requirement_total')->label('Kebutuhan'),
            TextColumn::make('status')->label('Status')
                ->formatStateUsing(fn ($state) => match ($state) {
                    'mencari_agen' => 'Mencari Agen',
                    'mencari_pekerja' => 'Mencari Pekerja',
                    'selesai' => 'Selesai',
                    default => ucfirst($state),
                }),

            TextColumn::make('close_date')->label('Batas Waktu'),

            TextColumn::make('workers_count')
                ->label('Jumlah Pekerja')
                ->counts('workers')
                ->default('-')
                ->sortable(),
        ])
        ->actions([
            ViewAction::make(),

            Action::make('Lihat Pekerja')
                ->label('Lihat Pekerja')
                ->icon('heroicon-o-users')
                ->url(fn (Recruitment $record) => route('filament.admin.resources.workers.index'))
                ->visible(fn (Recruitment $record) => $record->workers()->exists())
                ->color('primary'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRecruitments::route('/'),
            'create' => Pages\CreateRecruitment::route('/create'),
            'edit' => Pages\EditRecruitment::route('/{record}/edit'),
        ];
    }
}
