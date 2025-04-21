<?php

namespace App\Filament\Resources;

use App\Models\Application;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Storage;
use App\Filament\Resources\ApplicationResource\Pages;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;
    protected static ?string $navigationGroup = 'Agency';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Lamaran';

    public static function form(Form $form): Form
    {
        $isView = str(request()->route()?->getName())->contains('.view');

        return $form->schema([
            Select::make('recruitment_id')
                ->relationship('recruitment', 'position')
                ->label('Rekrutmen')
                ->required()
                ->disabled($isView),

            TextInput::make('name')->label('Nama Lengkap')->required()->disabled($isView),
            TextInput::make('phone')->label('Nomor Telepon')->required()->disabled($isView),
            TextInput::make('birth_place')->label('Tempat Lahir')->required()->disabled($isView),
            DatePicker::make('birth_date')->label('Tanggal Lahir')->required()->disabled($isView),
            Textarea::make('address')->label('Alamat Domisili')->required()->disabled($isView),

            FileUpload::make('cv')->label('Curriculum Vitae')->disk('public')->directory('cv')->downloadable()->disabled($isView),
            FileUpload::make('certificate')->label('Sertifikat Pelatihan')->disk('public')->directory('sertifikat')->downloadable()->disabled($isView),
            FileUpload::make('ijazah')->label('Fotokopi Ijazah')->disk('public')->directory('ijazah')->downloadable()->disabled($isView),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Nama')->sortable()->searchable(),

            TextColumn::make('cv')
                ->label('CV')
                ->formatStateUsing(fn ($state) => self::renderDownloadLink($state))
                ->html(),

            TextColumn::make('certificate')
                ->label('Sertifikat')
                ->formatStateUsing(fn ($state) => self::renderDownloadLink($state))
                ->html(),

            TextColumn::make('ijazah')
                ->label('Ijazah')
                ->formatStateUsing(fn ($state) => self::renderDownloadLink($state))
                ->html(),

            TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->colors([
                    'primary' => 'masuk',
                    'success' => 'seleksi',
                    'danger' => 'ditolak',
                ]),
        ])
        ->actions([
            ViewAction::make()->icon('heroicon-o-eye')->color('primary'),

            Action::make('Tolak')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (Application $record) => $record->status === 'masuk')
                ->action(fn (Application $record) => $record->update(['status' => 'ditolak'])),

            Action::make('Masuk Seleksi')
                ->label('Masuk Tahap Seleksi')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn (Application $record) => $record->status === 'masuk')
                ->action(function (Application $record) {
                    $record->update(['status' => 'seleksi']);
                    \App\Models\Selection::firstOrCreate(['application_id' => $record->id]);
                }),
        ]);
    }

    protected static function renderDownloadLink(?string $path): string
    {
        if (!$path) {
            return '<span class="text-sm text-gray-400 italic">-</span>';
        }

        $url = asset('storage/' . $path);
        return "<a href='{$url}' download class='text-primary underline'>Download</a>";
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'edit' => Pages\EditApplication::route('/{record}/edit'),
        ];
    }
}
