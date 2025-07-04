<?php

namespace App\Filament\Resources;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\TrainingParticipant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use App\Filament\Resources\TrainingParticipantResource\Pages\EditTrainingParticipant;
use App\Filament\Resources\TrainingParticipantResource\Pages\ListTrainingParticipants;
use App\Filament\Resources\TrainingParticipantResource\Pages\CreateTrainingParticipant;

class TrainingParticipantResource extends Resource
{
    protected static ?string $model = TrainingParticipant::class;
    protected static ?string $navigationLabel = 'Pendaftaran Pelatihan';
    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Pelamar';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Hidden::make('training_id')->default(fn () => request('training_id')),
            Hidden::make('user_id')->default(fn () => Auth::id()),
            TextInput::make('nama')->label('Nama Lengkap')->required(),
            TextInput::make('alamat')->label('Alamat')->required(),
            TextInput::make('tempat_lahir')->label('Tempat Lahir')->required(),
            DatePicker::make('tanggal_lahir')->label('Tanggal Lahir')->required(),
            Select::make('gender')->label('Jenis Kelamin')->options(['L'=>'Laki-laki', 'P'=>'Perempuan'])->required(),
            TextInput::make('no_ponsel')->label('No. Ponsel')->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->label('Nama'),
                TextColumn::make('training.tema_pelatihan')->label('Pelatihan'),
                TextColumn::make('training.sertifikasi.nama_sertifikasi')->label('Sertifikasi'),
                // TextColumn::make('alamat')->label('Alamat'),
                TextColumn::make('tempat_lahir')->label('Tempat Lahir'),
                TextColumn::make('tanggal_lahir')->label('Tanggal Lahir')->date(),
                TextColumn::make('gender')->label('Jenis Kelamin')
                    ->formatStateUsing(fn ($state) => $state == 'L' ? 'Laki-laki' : 'Perempuan'),
                TextColumn::make('no_ponsel')->label('No. Ponsel'),
                TextColumn::make('status')   // Kolom status
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(function($state) {
                        return match ($state) {
                            'menunggu' => 'Menunggu',
                            'kompeten' => 'Kompeten',
                            'tidak_kompeten' => 'Tidak Kompeten',
                            default => ucfirst($state)
                        };
                    })
                    ->colors([
                        'secondary' => 'menunggu',
                        'success' => 'kompeten',
                        'danger' => 'tidak_kompeten'
                    ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }



    public static function getPages(): array
    {
        return [
            'index' => ListTrainingParticipants::route('/'),
            'create'=> CreateTrainingParticipant::route('/create'),
            'edit'  => EditTrainingParticipant::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return Gate::allows('pendaftaran pelatihan');
    }
}
