<?php

namespace App\Filament\Resources;

use App\Models\Info;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use App\Filament\Resources\InfoResource\Pages\EditInfo;
use App\Filament\Resources\InfoResource\Pages\ListInfos;
use App\Filament\Resources\InfoResource\Pages\CreateInfo;

class InfoResource extends Resource
{
    protected static ?string $model = Info::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationLabel = 'Informasi';
    protected static ?string $navigationGroup = 'Konten Publik';

    public static function form(Form $form): Form
    {
        return $form->schema([
            FileUpload::make('image')->image()->directory('info-images'),
            TextInput::make('title')->required(),
            Textarea::make('description')->required()->rows(6),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->limit(30)->sortable()->searchable(),
                TextColumn::make('description')->limit(40)->wrap(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInfos::route('/'),
            'create' => CreateInfo::route('/create'),
            'edit' => EditInfo::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return Gate::allows('informasi');
    }
}

