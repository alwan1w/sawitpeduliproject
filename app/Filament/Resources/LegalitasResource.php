<?php

namespace App\Filament\Resources;

use App\Models\Legalitas;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Resource;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use App\Filament\Resources\LegalitasResource\Pages;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;

class LegalitasResource extends Resource
{
    protected static ?string $model = Legalitas::class;
    protected static ?string $navigationGroup = 'Agency';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Legalitas';

    public static function form(Form $form): Form
    {
        $isView = str(request()->route()?->getName())->contains('.view');

        return $form->schema([
            TextInput::make('nama_perusahaan')->required()->maxLength(100)->disabled($isView),
            Textarea::make('alamat')->required()->disabled($isView),
            TextInput::make('kontak')->required()->maxLength(20)->disabled($isView),
            TextInput::make('email')->email()->required()->maxLength(50)->disabled($isView),

            TextInput::make('akta')->label('Nomor & Tgl Akta')->nullable()->disabled($isView),
            TextInput::make('nib')->nullable()->disabled($isView),
            TextInput::make('suip')->nullable()->disabled($isView),
            TextInput::make('tdp')->nullable()->disabled($isView),
            TextInput::make('npwp')->nullable()->disabled($isView),
            TextInput::make('izin_operasional')->nullable()->disabled($isView),

            FileUpload::make('file_akta')->label('Upload Akta')->disk('public')->directory('legalitas')->disabled($isView),
            FileUpload::make('file_nib')->label('Upload NIB')->disk('public')->directory('legalitas')->disabled($isView),
            FileUpload::make('file_suip')->label('Upload SUIP')->disk('public')->directory('legalitas')->disabled($isView),
            FileUpload::make('file_tdp')->label('Upload TDP')->disk('public')->directory('legalitas')->disabled($isView),
            FileUpload::make('file_npwp')->label('Upload NPWP')->disk('public')->directory('legalitas')->disabled($isView),
            FileUpload::make('file_izin_operasional')->label('Upload Izin Operasional')->disk('public')->directory('legalitas')->disabled($isView),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama_perusahaan')->label('Nama'),
                TextColumn::make('email'),
                BadgeColumn::make('status')->colors([
                    'gray' => 'draft',
                    'warning' => 'menunggu_verifikasi',
                    'success' => 'terverifikasi',
                    'danger' => 'ditolak',
                ]),

                // File columns
                self::fileColumn('file_akta', 'Akta'),
                self::fileColumn('file_nib', 'NIB'),
                self::fileColumn('file_suip', 'SUIP'),
                self::fileColumn('file_tdp', 'TDP'),
                self::fileColumn('file_npwp', 'NPWP'),
                self::fileColumn('file_izin_operasional', 'Izin Operasional'),
            ])
            ->actions([
                EditAction::make()->visible(fn ($record) => $record->status === 'draft' || $record->status === 'ditolak'),
                DeleteAction::make()->visible(fn ($record) => $record->status === 'draft'),
                Action::make('Ajukan')
                    ->label('Ajukan ke Pemkab')
                    ->requiresConfirmation()
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->visible(fn ($record) => in_array($record->status, ['draft', 'ditolak']))
                    ->action(fn ($record) => $record->update(['status' => 'menunggu_verifikasi'])),
            ]);
    }

    protected static function fileColumn(string $field, string $label): TextColumn
    {
        return TextColumn::make($field)
            ->label($label)
            ->html()
            ->formatStateUsing(function (?string $state) {
                if (!$state) {
                    return '<span style="color: #9ca3af;">—</span>'; // abu-abu
                }

                $url = asset('storage/' . $state);
                return <<<HTML
                    <div style="text-align: center;">
                        <a href="{$url}" target="_blank" download style="color: #3b82f6; text-decoration: underline;">
                            Download
                        </a>
                    </div>
                HTML;
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLegalitas::route('/'),
            'create' => Pages\CreateLegalitas::route('/create'),
            'edit' => Pages\EditLegalitas::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('agency_id', Auth::id());
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['agency_id'] = Auth::id();
        $data['status'] = 'draft';
        return $data;
    }
}
