<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Application;
use App\Models\Recruitment;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\CheckboxList;
use App\Filament\Resources\ApplicationResource\Pages;

class ApplicationResource extends Resource
{
    protected static ?string $model = Application::class;
    protected static ?string $navigationGroup = 'Pelamar';
    protected static ?string $navigationIcon  = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Lamaran Saya';

    public static function form(Form $form): Form
    {
        // pull from config
        $types = config('documents.types');

        return $form->schema([

            Hidden::make('user_id')
                ->default(fn () => Auth::id()),
            Hidden::make('status')
                ->default('masuk'),

            Select::make('recruitment_id')
                ->label('Pilih Lowongan')
                ->options(
                    Recruitment::query()
                        ->where('status', 'mencari_pekerja')
                        ->with('company') // eager load Company supaya tidak N+1
                        ->get()
                        ->mapWithKeys(fn (Recruitment $r) => [
                            $r->id => "{$r->company->name} - {$r->position}",
                        ])
                        ->toArray()
                        )
                        ->searchable()
                        ->required(),

            TextInput::make('name')->label('Nama Lengkap')->required(),
            TextInput::make('phone')->label('No. Telepon')->required(),
            TextInput::make('birth_place')->label('Tempat Lahir')->required(),
            DatePicker::make('birth_date')->label('Tanggal Lahir')->required(),
            Textarea::make('address')->label('Alamat Domisili')->required(),

            // Ask which docs are required
            CheckboxList::make('required_documents')
                ->label('Dokumen yang Diperlukan')
                ->options($types)
                ->columns(2)
                ->required()
                ->reactive(),

            // Dynamically show one FileUpload per selected type
            FileUpload::make('documents.cv')
                ->label($types['cv'])
                ->directory('applications')
                ->nullable()
                ->visible(fn($get) => in_array('cv', (array) $get('required_documents'))),

            FileUpload::make('documents.sertifikat')
                ->label($types['sertifikat'])
                ->directory('applications')
                ->nullable()
                ->visible(fn($get) => in_array('sertifikat', (array) $get('required_documents'))),

            FileUpload::make('documents.ijazah')
                ->label($types['ijazah'])
                ->directory('applications')
                ->nullable()
                ->visible(fn($get) => in_array('ijazah', (array) $get('required_documents'))),

            FileUpload::make('documents.sk_sehat')
                ->label($types['sk_sehat'])
                ->directory('applications')
                ->nullable()
                ->visible(fn($get) => in_array('sk_sehat', (array) $get('required_documents'))),

            FileUpload::make('documents.pas_foto_3x4')
                ->label($types['pas_foto_3x4'])
                ->directory('applications')
                ->nullable()
                ->visible(fn($get) => in_array('pas_foto_3x4', (array) $get('required_documents'))),

            FileUpload::make('documents.ktp')
                ->label($types['ktp'])
                ->directory('applications')
                ->nullable()
                ->visible(fn($get) => in_array('ktp', (array) $get('required_documents'))),

            FileUpload::make('documents.kk')
                ->label($types['kk'])
                ->directory('applications')
                ->nullable()
                ->visible(fn($get) => in_array('kk', (array) $get('required_documents'))),

            FileUpload::make('documents.skck')
                ->label($types['skck'])
                ->directory('applications')
                ->nullable()
                ->visible(fn($get) => in_array('skck', (array) $get('required_documents'))),

            FileUpload::make('documents.transkrip_nilai')
                ->label($types['transkrip_nilai'])
                ->directory('applications')
                ->nullable()
                ->visible(fn($get) => in_array('transkrip_nilai', (array) $get('required_documents'))),

            FileUpload::make('documents.sim_a_c_b_b2')
                ->label($types['sim_a_c_b_b2'])
                ->directory('applications')
                ->nullable()
                ->visible(fn($get) => in_array('sim_a_c_b_b2', (array) $get('required_documents'))),

            // … repeat for each key in config/documents.php …
            // e.g. documents.sk_sehat, documents.pas_foto_3x4, etc.

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('recruitment.position')->label('Posisi'),
                TextColumn::make('recruitment.company.name')->label('Perusahaan'),
                TextColumn::make('name')->label('Pelamar'),
                TextColumn::make('phone')->label('Telepon'),

                // <-- PASTIKAN ADA DI SINI, BUKAN DI PROPERTY KELUARAN CLASS!
                BadgeColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match($state) {
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
                    ]),
            ])
            ->actions([
                ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'edit'   => Pages\EditApplication::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        // show only the logged-in user’s own applications
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        $data['status']  = 'masuk';
        $data['required_documents'] = $data['required_documents'] ?? [];
        // default
        return $data;
    }
}
