<?php

namespace App\Filament\Resources;

use App\Models\Selection;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Resources\SelectionResource\Pages;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;

class SelectionResource extends Resource
{
    protected static ?string $model = Selection::class;
    protected static ?string $navigationGroup = 'Agency';
    protected static ?string $navigationLabel = 'Peserta Seleksi';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        return $form->schema([]); // readonly, tanpa create manual
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('application.name')->label('Nama'),
            TextColumn::make('application.recruitment.position')->label('Posisi'),
            TextColumn::make('status')->badge(),
        ])
        ->filters([
            //
        ])
        ->actions([
            ViewAction::make(),

            Action::make('Gagal')
                ->requiresConfirmation()
                ->color('danger')
                ->visible(fn (Selection $record) => $record->status === null)
                ->action(function (Selection $record) {
                    $record->update(['status' => 'gagal']);
                }),

                Action::make('Lolos')
                ->color('success')
                ->visible(fn (Selection $record) => $record->status === null)
                ->action(function (Selection $record) {
                    $record->update(['status' => 'lolos']);

                    $recruitment = $record->application->recruitment;

                    // Tambahkan worker dengan recruitment_id!
                    \App\Models\Worker::create([
                        'application_id' => $record->application_id,
                        'company_id' => $recruitment->company_id,
                        'recruitment_id' => $recruitment->id, // << tambahkan ini!
                    ]);

                    // Update status recruitment kalau sudah penuh
                    $jumlahLolos = $recruitment->workers()->count();
                    if ($jumlahLolos >= $recruitment->requirement_total) {
                        $recruitment->update(['status' => 'selesai']);
                    }
                }),

        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSelections::route('/'),
        ];
    }
}
