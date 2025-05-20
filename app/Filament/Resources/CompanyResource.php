<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Company;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CompanyResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Filament\Resources\CompanyResource\Pages\EditCompany;
use App\Filament\Resources\CompanyResource\Pages\CreateCompany;
use App\Filament\Resources\CompanyResource\Pages\ListCompanies;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationLabel = 'Perusahaan';
    protected static ?string $navigationGroup = 'Perusahaan';


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->label('Nama Perusahaan')->required(),
            Forms\Components\TextInput::make('director')->label('Nama Direktur')->required(),
            Forms\Components\TextInput::make('phone')->label('Kontak'),
            Forms\Components\TextInput::make('email')->label('Email'),
            Forms\Components\Textarea::make('address')->label('Alamat')->rows(2),

            Forms\Components\TextInput::make('akta')->label('No. Akta'),
            Forms\Components\TextInput::make('nib')->label('NIB'),
            Forms\Components\TextInput::make('tdp')->label('TDP'),
            Forms\Components\TextInput::make('suip')->label('SUIP'),
            Forms\Components\TextInput::make('npwp')->label('NPWP'),
            Forms\Components\TextInput::make('izin_operasional')->label('Izin Operasional'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Nama'),
            TextColumn::make('director')->label('Direktur'),
            TextColumn::make('phone')->label('Kontak'),
            TextColumn::make('email'),
            TextColumn::make('workers_count')->label('Pekerja')->counts('workers'),
        ])->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'edit' => Pages\EditCompany::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->withCount(['workers']);
    }

    public static function canAccess(): bool
    {
        return Gate::allows('akses_perusahaan');
    }
}
