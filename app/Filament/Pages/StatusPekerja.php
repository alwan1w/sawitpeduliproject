<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use App\Models\Worker;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Concerns\InteractsWithForms;

class StatusPekerja extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationGroup = 'Pekerja';
    protected static ?string $navigationLabel = 'Status Saya';
    protected static ?string $title = 'Status Saya';

    // ⚠️ WAJIB ditambahkan view default agar tidak error
    protected static string $view = 'filament.pages.status-pekerja';

    public ?Worker $worker = null;

    public function mount(): void
    {
        $this->worker = Worker::where('user_id', Auth::id())
            ->with(['application.recruitment.company', 'application.user'])
            ->first();

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        $w = $this->worker;

        // Cek worker dan semua relasi yang wajib
        if (
            !$w ||
            !$w->application ||
            !$w->application->recruitment ||
            !$w->application->recruitment->company ||
            !$w->application->user
        ) {
            return $form->schema([
                Placeholder::make('no-data')->label('Informasi')->content('Data belum tersedia.'),
            ]);
        }

        $durasi = Carbon::parse($w->start_date)->diffInMonths($w->batas_kontrak);

        $sertifikasiNames = \App\Models\TrainingParticipant::where('user_id', $w->application->user->id)
            ->where('status', 'kompeten')
            ->with('training.sertifikasi')
            ->get()
            ->pluck('training.sertifikasi.nama_sertifikasi')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        return $form->schema([

            Section::make('Foto Profil')->schema([
                Placeholder::make('')
                    // ->label('Foto Profil')
                    ->content(function () use ($w) {
                        $photo = $w->application->profile_photo;
                        $name = $w->application->name ?? 'User';
                        $url = $photo
                            ? \Illuminate\Support\Facades\Storage::url($photo)
                            : "https://ui-avatars.com/api/?name=" . urlencode($name);

                        return new \Illuminate\Support\HtmlString(
                            '<img src="' . $url . '" style="width:96px;height:96px;border-radius:999px;object-fit:cover;background:#222;" alt="Foto Profil">'
                        );
                    }),
            ]),

            Section::make('Informasi Pekerja')->schema([
                Placeholder::make('nama')->label('Nama Lengkap')->content($w->application->name),
                Placeholder::make('posisi')->label('Posisi')->content($w->application->recruitment->position),
                Placeholder::make('telepon')->label('Telepon')->content($w->application->phone),
                Placeholder::make('tempat_lahir')->label('Tempat Lahir')->content($w->application->birth_place),
            ])->columns(2),

            Section::make('Detail Kontrak')->schema([
                Placeholder::make('perusahaan')->label('Perusahaan')->content($w->application->recruitment->company->name),
                Placeholder::make('durasi')->label('Durasi Kontrak')->content("{$durasi} bulan"),
                Placeholder::make('awal_kontrak')->label('Awal Kontrak')->content($w->start_date->format('d M Y')),
                Placeholder::make('akhir_kontrak')->label('Akhir Kontrak')->content($w->batas_kontrak->format('d M Y')),
            ])->columns(2),

            Section::make('Sertifikasi yang Dimiliki')->schema([
                Placeholder::make('list_sertifikasi')
                    ->label('')
                    ->content(function () use ($sertifikasiNames) {
                        if (empty($sertifikasiNames)) {
                            return new \Illuminate\Support\HtmlString('<i>Tidak ada sertifikasi kompeten.</i>');
                        }
                        $html = '<ul style="padding-left:1em;margin:0;">' .
                            implode('', array_map(fn($s) => "<li>{$s}</li>", $sertifikasiNames)) .
                            '</ul>';
                        return new \Illuminate\Support\HtmlString($html);
                    }),

            ]),

            Section::make('Informasi Perusahaan')->schema([
                Placeholder::make('alamat_perusahaan')->label('Alamat')->content($w->application->recruitment->company->address),
                Placeholder::make('kontak_perusahaan')->label('Kontak')->content($w->application->recruitment->company->phone),
            ]),
        ]);
    }


    public static function canAccess(): bool
    {
        return Gate::allows('akses status');
    }
}
