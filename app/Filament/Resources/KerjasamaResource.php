<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Actions\ViewAction;
use App\Models\Recruitment;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\KerjasamaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KerjasamaResource\RelationManagers;

class KerjasamaResource extends Resource
{
    protected static ?string $model = Recruitment::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Agency';
    protected static ?string $navigationLabel = 'Kerjasama';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('position')
                ->label('Posisi')
                ->disabled(),

            Forms\Components\Textarea::make('detail_posisi')
                ->label('Detail Posisi')
                ->disabled(),

            Forms\Components\TextInput::make('requirement_total')
                ->label('Jumlah Dibutuhkan')
                ->disabled(),

            Forms\Components\DatePicker::make('open_date')
                ->label('Tanggal Dibuka')
                ->disabled(),

            Forms\Components\DatePicker::make('close_date')
                ->label('Tanggal Ditutup')
                ->disabled(),

            Forms\Components\TextInput::make('salary_range')
                ->label('Gaji')
                ->disabled(),

            Forms\Components\TextInput::make('contract_duration')
                ->label('Durasi Kontrak')
                ->disabled(),

            Forms\Components\Textarea::make('skills')
                ->label('Keterampilan')
                ->disabled(),

            Forms\Components\TextInput::make('age_range')
                ->label('Usia')
                ->disabled(),

            Forms\Components\TextInput::make('education')
                ->label('Pendidikan')
                ->disabled(),

            Forms\Components\TextInput::make('status')
                ->label('Status')
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')->label('Perusahaan'),
                TextColumn::make('position')->label('Permintaan Posisi'),
                TextColumn::make('requirement_total')->label('Jumlah Kebutuhan'),
                TextColumn::make('close_date')->label('Batas Waktu'),
            ])
            ->actions([

                ViewAction::make(),

                Action::make('Terima')
                    ->label('Terima Permintaan')
                    ->color('success')
                    ->action(function (Recruitment $record) {
                        $record->agency_id = Auth::id();
                        $record->status = 'mencari_pekerja';
                        $record->save();
                    })
                    ->visible(fn (Recruitment $record) => $record->agency_id === null && $record->status === 'mencari_agen'),
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
            'index' => Pages\ListKerjasamas::route('/'),

        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'mencari_agen')
            ->whereNull('agency_id');
    }
}
