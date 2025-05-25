<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use App\Models\Worker;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Application;
use App\Models\Recruitment;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
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
        return $form->schema([
            // auto-set user_id dan default status
            Hidden::make('user_id')
                ->default(fn () => Auth::id()),

            Hidden::make('status')
                ->default('masuk'),

            // Pilih lowongan
            Select::make('recruitment_id')
                ->label('Pilih Lowongan')
                ->options(
                    Recruitment::query()
                        ->where('status', 'mencari_pekerja')
                        ->with('company')
                        ->get()
                        ->mapWithKeys(fn($r) => [
                            $r->id => "{$r->company->name} - {$r->position}"
                        ])
                        ->toArray()
                )
                ->reactive()
                ->required(),

            TextInput::make('name')
                ->label('Nama Lengkap')
                ->required(),

            TextInput::make('phone')
                ->label('No. Telepon')
                ->required(),

            TextInput::make('birth_place')
                ->label('Tempat Lahir')
                ->required(),

            DatePicker::make('birth_date')
                ->label('Tanggal Lahir')
                ->required(),

            TextInput::make('address')
                ->label('Alamat Domisili')
                ->columnSpanFull()
                ->required(),

            // Tampilkan upload field sesuai lowongan yang dipilih
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
                ->columns(2),


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
                ViewAction::make(),
                EditAction::make(),
                Action::make('konfirmasi')
                    ->label('Konfirmasi Bergabung')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        DB::transaction(function () use ($record) {
                            // Cek apakah sudah ada Worker dengan user_id ini
                            $workerExists = Worker::where('user_id', $record->user_id)->exists();
                            if ($workerExists) {
                                Notification::make()
                                    ->title('Pekerja sudah terdaftar sebelumnya!')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            // Update status lamaran terpilih
                            $record->update(['status' => 'dikonfirmasi']);

                            // Tolak semua lamaran lain
                            Application::where('user_id', $record->user_id)
                                ->where('id', '!=', $record->id)
                                ->update(['status' => 'ditolak']);

                            // Ubah role user menjadi pekerja
                            $record->user->syncRoles(['pekerja']);

                            // Ambil durasi kontrak
                            $durasiBulan = $record->recruitment->contract_duration;

                            // Hitung tanggal mulai dan batas kontrak
                            $startDate = now();
                            $batasKontrak = $startDate->copy()->addMonths($durasiBulan);

                            // Buat data di tabel Worker
                            Worker::create([
                                'application_id' => $record->id,
                                'recruitment_id' => $record->recruitment_id,
                                'company_id'     => $record->recruitment->company_id,
                                'user_id'        => $record->user_id,
                                'start_date'     => $startDate,
                                'batas_kontrak'  => $batasKontrak,
                            ]);
                        });
                    })
                    ->visible(fn ($record) => $record->status === 'diterima'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListApplications::route('/'),
            'create' => Pages\CreateApplication::route('/create'),
            'view'   => Pages\ViewApplication::route('/{record}'),
            'edit'   => Pages\EditApplication::route('/{record}/edit'),  // <— pastikan ada

        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        // hanya lamaran user tersebut
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

    public static function canAccess(): bool
    {
        return Gate::allows('akses lamaran');
    }
}
