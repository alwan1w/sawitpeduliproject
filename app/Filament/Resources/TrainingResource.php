<?php

namespace App\Filament\Resources;

use App\Models\Training;
use App\Models\Materi;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Resources\TrainingResource\Pages;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;

class TrainingResource extends Resource
{
    protected static ?string $model = Training::class;
    protected static ?string $navigationLabel = 'Pelatihan';
    protected static ?string $navigationGroup = 'Pemkab';
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('tema_pelatihan')->label('Tema Pelatihan')->maxLength(200)->required(),
            Forms\Components\TextInput::make('moderator')->label('Nama Moderator')->maxLength(100)->required(),
            Forms\Components\DatePicker::make('tanggal_pelatihan')->label('Tanggal Pelatihan')->required(),
            Forms\Components\Select::make('materi_id')
                ->label('Pilih Materi')
                ->options(Materi::pluck('judul_materi', 'id'))
                ->searchable()
                ->required(),
            Forms\Components\TextInput::make('kuota_peserta')->label('Kuota Peserta')->numeric()->required(),
            Forms\Components\TextInput::make('lokasi')->label('Lokasi Pelatihan')->maxLength(256)->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tema_pelatihan')->label('Tema')->searchable(),
                Tables\Columns\TextColumn::make('materi.judul_materi')->label('Materi'),
                Tables\Columns\TextColumn::make('moderator')->label('Moderator'),
                Tables\Columns\TextColumn::make('tanggal_pelatihan')->label('Tanggal')->date('d M Y'),
                Tables\Columns\TextColumn::make('kuota_peserta')->label('Kuota'),
                Tables\Columns\TextColumn::make('lokasi')->label('Lokasi'),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrainings::route('/'),
            'create' => Pages\CreateTraining::route('/create'),
            'edit' => Pages\EditTraining::route('/{record}/edit'),
        ];
    }
}
