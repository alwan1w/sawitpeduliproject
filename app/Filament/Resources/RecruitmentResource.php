<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Recruitment;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\RecruitmentResource\Pages;
use App\Filament\Resources\RecruitmentResource\RelationManagers;

class RecruitmentResource extends Resource
{
    protected static ?string $model = Recruitment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('position')->required(),
            Textarea::make('detail_posisi')
                ->label('Detail Posisi')
                ->rows(3)
                ->maxLength(500)
                ->columnSpanFull(),
            TextInput::make('requirement_total')->numeric()->required(),
            DatePicker::make('open_date')->required(),
            DatePicker::make('close_date')->required(),
            TextInput::make('salary_range'),
            TextInput::make('contract_duration'),
            Textarea::make('skills'),
            TextInput::make('age_range'),
            TextInput::make('education'),
            TextInput::make('status')
                ->default('mencari_agen')
                ->disabled(),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('position')->label('Posisi'),
            TextColumn::make('detail_posisi')->label('Detail Posisi')->limit(50)->wrap(),
            TextColumn::make('agency.name')->label('Agen')->default('-'),
            TextColumn::make('requirement_total')->label('Kebutuhan'),
            TextColumn::make('status')->label('Status')
                ->formatStateUsing(fn ($state) => match($state) {
                    'mencari_agen' => 'Mencari Agen',
                    'mencari_pekerja' => 'Mencari Pekerja',
                    'selesai' => 'Selesai',
                }),
            TextColumn::make('close_date')->label('Batas Waktu'),
        ])
        ->actions([
            ViewAction::make(),

        ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('company_id', Auth::id());
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = Auth::id();
        $data['status'] = 'mencari_agen';
        return $data;
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
            'index' => Pages\ListRecruitments::route('/'),
            'create' => Pages\CreateRecruitment::route('/create'),
            'edit' => Pages\EditRecruitment::route('/{record}/edit'),
        ];
    }
}
