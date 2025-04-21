<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Legalitas;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\LegalitasResource;
use App\Filament\Resources\LegalitasVerifikasiResource\Pages;
use App\Filament\Resources\LegalitasVerifikasiResource\Pages\ListLegalitasVerifikasis;
use App\Filament\Resources\LegalitasVerifikasiResource\Pages\ViewLegalitasVerifikasis;

class LegalitasVerifikasiResource extends Resource
{
    protected static ?string $model = Legalitas::class;
    protected static ?string $navigationLabel = 'Verifikasi Legalitas Agen';
    protected static ?string $navigationIcon = 'heroicon-o-check-circle';
    protected static ?string $navigationGroup = 'Pemkab';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nama_perusahaan')->label('Nama Perusahaan')->disabled(),
            Textarea::make('alamat')->label('Alamat')->disabled(),
            TextInput::make('kontak')->label('Kontak')->disabled(),
            TextInput::make('email')->label('Email')->disabled(),

            TextInput::make('akta')->label('Nomor & Tgl Akta')->disabled(),
            TextInput::make('nib')->label('NIB')->disabled(),
            TextInput::make('suip')->label('SUIP')->disabled(),
            TextInput::make('tdp')->label('TDP')->disabled(),
            TextInput::make('npwp')->label('NPWP')->disabled(),
            TextInput::make('izin_operasional')->label('Izin Operasional')->disabled(),

            Textarea::make('file_akta')->label('File Akta')->disabled()
                ->default(fn ($record) => $record->file_akta
                    ? asset('storage/' . $record->file_akta)
                    : 'Tidak ada'),

            Textarea::make('file_nib')->label('File NIB')->disabled()
                ->default(fn ($record) => $record->file_nib
                    ? asset('storage/' . $record->file_nib)
                    : 'Tidak ada'),

            Textarea::make('file_suip')->label('File SUIP')->disabled()
                ->default(fn ($record) => $record->file_suip
                    ? asset('storage/' . $record->file_suip)
                    : 'Tidak ada'),

            Textarea::make('file_tdp')->label('File TDP')->disabled()
                ->default(fn ($record) => $record->file_tdp
                    ? asset('storage/' . $record->file_tdp)
                    : 'Tidak ada'),

            Textarea::make('file_npwp')->label('File NPWP')->disabled()
                ->default(fn ($record) => $record->file_npwp
                    ? asset('storage/' . $record->file_npwp)
                    : 'Tidak ada'),

            Textarea::make('file_izin_operasional')->label('File Izin Operasional')->disabled()
                ->default(fn ($record) => $record->file_izin_operasional
                    ? asset('storage/' . $record->file_izin_operasional)
                    : 'Tidak ada'),
        ]);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('agency.name')->label('Nama Agen'),
                TextColumn::make('alamat')->label('Alamat')->limit(40),
                TextColumn::make('created_at')->label('Waktu Pengajuan')->time(),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'menunggu_verifikasi',
                        'success' => 'terverifikasi',
                        'danger' => 'ditolak',
                    ]),
            ])
            ->actions([
                ViewAction::make(),

                Action::make('verifikasi')
                    ->label('Verifikasi')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn ($record) => $record->status === 'menunggu_verifikasi')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'terverifikasi'])),

                Action::make('tolak')
                    ->label('Tolak')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->visible(fn ($record) => $record->status === 'menunggu_verifikasi')
                    ->requiresConfirmation()
                    ->action(fn ($record) => $record->update(['status' => 'ditolak'])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLegalitasVerifikasis::route('/'),

        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('status', 'menunggu_verifikasi');
    }
}
