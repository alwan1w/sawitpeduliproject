<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Application;
use App\Models\Recruitment;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\LamaranMasukResource\Pages;

class LamaranMasukResource extends Resource
{
    protected static ?string $model = Application::class;
    protected static ?string $navigationGroup = 'Agency';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Lamaran Masuk';
    protected static ?string $navigationIcon  = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Lowongan yang dilamar
            Placeholder::make('lowongan')
                ->label('Lowongan')
                ->content(fn ($record) =>
                    ($record->recruitment->company->name ?? '-') .
                    ' - ' .
                    ($record->recruitment->position ?? '-')
                ),

            FileUpload::make('profile_photo')
                ->label('Foto Profil')
                ->image()
                ->directory('profile_photos')
                ->maxSize(1024)
                ->preserveFilenames()
                ->openable()
                ->downloadable()
                ->disabled(),

            // Data Pelamar
            Forms\Components\TextInput::make('name')
                ->label('Nama Lengkap')
                ->disabled(),

            Forms\Components\TextInput::make('phone')
                ->label('Telepon')
                ->disabled(),

            Forms\Components\TextInput::make('birth_place')
                ->label('Tempat Lahir')
                ->disabled(),

            Forms\Components\DatePicker::make('birth_date')
                ->label('Tanggal Lahir')
                ->disabled(),

            Forms\Components\Textarea::make('address')
                ->label('Alamat Domisili')
                ->disabled(),

            // Dokumen yang diajukan
            Forms\Components\Section::make('Unggah Dokumen')
                ->schema(function (callable $get) {
                    // ambil array required_documents dari recruitment
                    $reqs = [];
                    if ($rid = $get('recruitment_id')) {
                        $reqs = Recruitment::find($rid)?->required_documents ?? [];
                    }
                    // bangun schema FileUpload
                    return collect($reqs)
                        ->map(fn(string $doc) => [
                            'component' => FileUpload::make('documents.' . Str::slug($doc, '_'))
                                ->label($doc)
                                ->directory('applications')
                                ->openable()
                                ->downloadable()
                                ->preserveFilenames()
                                ->required(),
                        ])
                        ->pluck('component')
                        ->toArray();
                })
                ->visible(fn(callable $get) => ! empty($get('recruitment_id')))
                ->columns(2)
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_photo')
                    ->label('Foto')
                    ->circular()
                    ->height(40)
                    ->width(40)
                    ->getStateUsing(fn ($record) => $record->profile_photo ?: 'https://ui-avatars.com/api/?name=' . urlencode($record->name)),
                TextColumn::make('name')->label('Nama')->sortable()->searchable(),
                TextColumn::make('phone')->label('Telepon'),
                TextColumn::make('recruitment.position')->label('Posisi'),
                TextColumn::make('recruitment.company.name')->label('Perusahaan'),
               BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'masuk'    => 'Masuk',
                        'seleksi'  => 'Seleksi',
                        'ditolak'  => 'Ditolak',
                        'diterima' => 'Diterima',
                        default    => ucfirst($state),
                    })
                    ->colors([
                        'secondary' => 'masuk',
                        'warning'   => 'seleksi',
                        'danger'    => 'ditolak',
                        'success'   => 'diterima',
                    ])
            ])
            ->actions([
                ViewAction::make(),    // akan membuka ViewPage
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLamaranMasuks::route('/'),
            'view'  => Pages\ViewLamaranMasuk::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        // Hanya lamaran untuk recruitment yang di-handle agent ini
        return parent::getEloquentQuery()
            ->whereHas('recruitment', fn ($q) => $q->where('agency_id', Auth::id()))
            ->where('status', 'masuk');
    }

    public static function canAccess(): bool
    {
        return Gate::allows('akses lamaran masuk');
    }
}
