<?php

namespace App\Filament\Resources;

use App\Models\Worker;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Resources\WorkerResource\Pages;
use Filament\Tables\Columns\TextColumn;

class WorkerResource extends Resource
{
    protected static ?string $model = Worker::class;
    protected static ?string $navigationGroup = 'Perusahaan';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Kelola Pekerja';

    public static function form(Form $form): Form
    {
        // Tidak pakai form input (read-only table)
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('application.name')
                ->label('Nama Pekerja')
                ->searchable(),

            TextColumn::make('application.recruitment.position')
                ->label('Posisi'),

            TextColumn::make('company.name')
                ->label('Perusahaan'),

            TextColumn::make('start_date')
                ->label('Tanggal Mulai')
                ->date('d M Y')
                ->sortable(),
        ])
        ->defaultSort('start_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkers::route('/'), // pastikan ListWorkers.php file-nya bener
        ];
    }
}
