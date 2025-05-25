<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Materi;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Gate;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\DeleteAction;
use App\Filament\Resources\MateriResource\Pages;

class MateriResource extends Resource
{
    protected static ?string $model = Materi::class;
    protected static ?string $navigationLabel = 'Materi Pelatihan';
    protected static ?string $navigationGroup = 'Pemkab';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('judul_materi')
                ->label('Judul Materi Pelatihan')
                ->maxLength(100)
                ->required(),

            Forms\Components\FileUpload::make('file')
                ->label('Upload File Materi')
                ->acceptedFileTypes(['application/pdf'])
                ->directory('materi-files'),

            Forms\Components\TextInput::make('tujuan')
                ->label('Tujuan Pelatihan')
                ->maxLength(200),

            Forms\Components\TextInput::make('deskripsi')
                ->label('Deskripsi Singkat')
                ->maxLength(200),

            Forms\Components\Textarea::make('isi_materi')
                ->label('Isi Materi')
                ->maxLength(1200),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul_materi')->label('Judul Materi')->searchable(),
                Tables\Columns\TextColumn::make('file')->label('File')
                    ->formatStateUsing(fn ($state) => $state ? basename($state) : '-')
                    ->url(fn ($record) => $record->file ? asset('storage/' . $record->file) : null, true)
                    ->openUrlInNewTab(),
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
            'index' => Pages\ListMateris::route('/'),
            'create' => Pages\CreateMateri::route('/create'),
            'edit' => Pages\EditMateri::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return Gate::allows('akses materi');
    }
}
