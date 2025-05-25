<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Sertifikasi;
use Filament\Resources\Resource;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SertifikasiResource\Pages;
use App\Filament\Resources\SertifikasiResource\RelationManagers;

class SertifikasiResource extends Resource
{
    protected static ?string $model = Sertifikasi::class;
    protected static ?string $navigationLabel = 'Sertifikasi';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Pemkab';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('nama_sertifikasi')
                ->label('Nama Sertifikasi')
                ->required(),
            TextInput::make('deskripsi')
                ->label('Deskripsi')
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
       return $table->columns([
            Tables\Columns\TextColumn::make('nama_sertifikasi')->label('Nama Sertifikasi'),
            Tables\Columns\TextColumn::make('deskripsi')->label('Deskripsi')->limit(50),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSertifikasis::route('/'),
            'create' => Pages\CreateSertifikasi::route('/create'),
            'edit' => Pages\EditSertifikasi::route('/{record}/edit'),
        ];
    }
}
