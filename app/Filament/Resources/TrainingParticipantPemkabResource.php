<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\TrainingParticipant;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use App\Models\TrainingParticipantPemkab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TrainingParticipantPemkabResource\Pages;
use App\Filament\Resources\TrainingParticipantPemkabResource\RelationManagers;

class TrainingParticipantPemkabResource extends Resource
{
    protected static ?string $model = TrainingParticipant::class;
    protected static ?string $navigationLabel = 'Peserta Pelatihan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->searchable(),
                TextColumn::make('training.tema_pelatihan')->label('Pelatihan')->searchable(),
                TextColumn::make('status')
                    ->searchable()   // Kolom status
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
            ->actions([
                Action::make('kompeten')
                    ->label('Kompeten')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update(['status' => 'kompeten']))
                    ->visible(fn($record) => $record->status === 'menunggu'),

                Action::make('tidak_kompeten')
                    ->label('Tidak Kompeten')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update(['status' => 'tidak_kompeten']))
                    ->visible(fn($record) => $record->status === 'menunggu'),

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
            'index' => Pages\ListTrainingParticipantPemkabs::route('/'),
            'create' => Pages\CreateTrainingParticipantPemkab::route('/create'),
            'edit' => Pages\EditTrainingParticipantPemkab::route('/{record}/edit'),
        ];
    }
}
