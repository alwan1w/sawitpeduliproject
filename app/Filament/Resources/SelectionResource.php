<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Worker;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Application;
use App\Models\Recruitment;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\SelectionResource\Pages;

class SelectionResource extends Resource
{
    protected static ?string $model = Application::class;
    protected static ?string $navigationGroup = 'Agency';
    protected static ?string $navigationLabel = 'Peserta Seleksi';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';

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
        return $table->columns([
            TextColumn::make('name')->label('Nama')->sortable()->searchable(),
            TextColumn::make('recruitment.position')->label('Posisi'),
            TextColumn::make('phone')->label('Telepon'),
            TextColumn::make('birth_place')->label('Tempat Lahir'),
            TextColumn::make('birth_date')->label('Tanggal Lahir')->date('d F Y'),
            TextColumn::make('address')->label('Alamat')->limit(30),
            TextColumn::make('dokumen_status')
            ->label('Dokumen')
            ->state(function ($record) {
                $requiredDocs = $record->recruitment->required_documents ?? [];
                $documents = $record->documents ?? [];
                $missing = array_diff(
                    array_map(fn($doc) => \Illuminate\Support\Str::slug($doc, '_'), $requiredDocs),
                    array_keys($documents)
                );

                return empty($missing)
                    ? '<span style="color: green;">Lengkap</span>'
                    : '<span style="color: red;">Kurang: ' . implode(', ', array_map(fn($slug) => \Illuminate\Support\Str::title(str_replace('_', ' ', $slug)), $missing)) . '</span>';
            })
            ->html(),
            TextColumn::make('status')->badge(),
        ])
        ->actions([
            ViewAction::make(),

            Action::make('Gagal')
                ->requiresConfirmation()
                ->color('danger')
                ->visible(fn ($record) => $record->status === 'seleksi')
                ->action(function ($record) {
                    $record->update(['status' => 'ditolak']);
                }),

            Action::make('Lolos')
                ->color('success')
                ->visible(fn ($record) => $record->status === 'seleksi')
                ->action(function ($record) {
                    $record->update(['status' => 'diterima']);

                    $recruitment = $record->recruitment;

                    // Worker::create([
                    //     'application_id' => $record->id,
                    //     'company_id' => $recruitment->company_id,
                    //     'recruitment_id' => $recruitment->id,
                    //     'user_id'        => $record->user_id,
                    // ]);

                    $jumlahLolos = $recruitment->workers()->count();
                    if ($jumlahLolos >= $record->recruitment->requirement_total) {
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

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'seleksi')
            ->whereHas('recruitment', fn ($q) => $q->where('agency_id', Auth::id()));
    }

    public static function canAccess(): bool
    {
        return Gate::allows('akses seleksi');
    }
}
