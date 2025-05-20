<?php

namespace App\Filament\Pages;

use Carbon\Carbon;
use App\Models\Worker;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;

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
            ->with('application.recruitment.company')
            ->firstOrFail();

        $this->form->fill(); // isi form kosong karena hanya Placeholder
    }

    public function form(Form $form): Form
    {
        $w = $this->worker;

        if (!$w || !$w->application || !$w->application->recruitment || !$w->application->recruitment->company) {
            return $form->schema([
                Placeholder::make('no-data')->label('Informasi')->content('Data belum tersedia.'),
            ]);
        }

        $durasi = Carbon::parse($w->start_date)->diffInMonths($w->batas_kontrak);

        return $form->schema([
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

            Section::make('Informasi Perusahaan')->schema([
                Placeholder::make('alamat_perusahaan')->label('Alamat')->content($w->application->recruitment->company->address),
                Placeholder::make('kontak_perusahaan')->label('Kontak')->content($w->application->recruitment->company->kontak),
            ]),
        ]);
    }
}
