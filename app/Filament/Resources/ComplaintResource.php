<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Complaint;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use App\Filament\Resources\ComplaintResource\Pages;

class ComplaintResource extends Resource
{
    protected static ?string $model = Complaint::class;
    protected static ?string $navigationLabel = 'Pengaduan';
    protected static ?string $navigationGroup = 'Pekerja';
    protected static ?string $navigationIcon  = 'heroicon-o-chat-bubble-bottom-center-text';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('subject')
                ->label('Judul Pengaduan')
                ->required(),

            Forms\Components\Hidden::make('worker_id')
                ->default(fn () => Auth::id()),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('subject')->label('Laporan'),
            Tables\Columns\TextColumn::make('status')->label('Status'),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Waktu')
                ->formatStateUsing(fn($state) =>
                    \Carbon\Carbon::parse($state)
                        ->setTimezone('Asia/Jakarta') // atau 'Asia/Bangkok'
                        ->format('H:i')
                )
        ])
        ->actions([
            ViewAction::make()->url(fn($record) => static::getUrl('view', ['record' => $record])),
            EditAction::make()
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListComplaints::route('/'),
            'create' => Pages\CreateComplaint::route('/create'),
            'edit'   => Pages\EditComplaint::route('/{record}/edit'),
            'view'   => Pages\ViewComplaint::route('/{record}'),
        ];
    }

    // Hanya tampilkan pengaduan milik pekerja login
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('worker_id', Auth::id());
    }

    public static function canAccess(): bool
    {
        return Gate::allows('pengaduan');
    }
}
