<?php

namespace App\Filament\Resources;

use App\Models\Worker;
use App\Models\Company;
use Filament\Tables\Table;
use App\Models\Recruitment;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Gate;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\AllWorkerResource\Pages\ListAllWorkers;

class AllWorkerResource extends Resource
{
    protected static ?string $model = Worker::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Semua Pekerja';
    protected static ?string $navigationGroup = 'Pengawas';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('application.name')->label('Nama')->searchable(),
                TextColumn::make('application.recruitment.position')->label('Posisi')->searchable(),
                TextColumn::make('application.recruitment.company.name')->label('Perusahaan')->searchable(),
                TextColumn::make('start_date')->label('Mulai')->date('d M Y'),
                TextColumn::make('batas_kontrak')->label('Akhir Kontrak')->date('d M Y'),
                TextColumn::make('status_kontrak')->label('Status Kontrak'),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->label('Perusahaan')
                    ->options(fn () => Company::orderBy('name')->pluck('name', 'id')->toArray())
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        if (! $data['value']) return $query;
                        return $query->whereHas('application.recruitment', fn ($q) => $q->where('company_id', $data['value']));
                    }),

                SelectFilter::make('position')
                    ->label('Posisi')
                    ->options(fn () => Recruitment::orderBy('position')->pluck('position', 'position')->unique()->toArray())
                    ->query(function (\Illuminate\Database\Eloquent\Builder $query, array $data): \Illuminate\Database\Eloquent\Builder {
                        if (! $data['value']) return $query;
                        return $query->whereHas('application.recruitment', fn ($q) => $q->where('position', $data['value']));
                    }),
            ])
            ->defaultSort('start_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAllWorkers::route('/'),
        ];
    }

    public static function canAccess(): bool
    {
        return Gate::allows('semua pekerja');
    }
}
