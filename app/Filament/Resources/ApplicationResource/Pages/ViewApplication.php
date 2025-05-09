<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use Nette\Utils\Html;
use Filament\Actions\Action;
use Filament\Forms\Components\Card;
use Illuminate\Support\Facades\Storage;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Placeholder;
use App\Filament\Resources\ApplicationResource;

class ViewApplication extends ViewRecord
{
    protected static string $resource = ApplicationResource::class;

    // (Optional) Tombol header, misal Edit atau Delete bisa ditambahkan di sini.
    protected function getHeaderActions(): array
    {
        return [
            // contoh tombol Edit, kalau mau diaktifkan:
            Action::make('edit')
                ->label('Edit Lamaran')
                ->url($this->getResource()::getUrl('edit', ['record' => $this->record]))
                ->icon('heroicon-o-pencil'),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Card::make()->schema([
                // Lowongan yang dilamar: Perusahaan – Posisi
                Placeholder::make('lowongan')
                    ->label('Lowongan')
                    ->content(fn ($record) =>
                        ($record->recruitment->company->name ?? '-')
                        . ' - '
                        . ($record->recruitment->position ?? '-')
                    ),

                // Data Pelamar
                Placeholder::make('name')
                    ->label('Nama Lengkap')
                    ->content(fn ($record) => $record->name),
                Placeholder::make('phone')
                    ->label('No. Telepon')
                    ->content(fn ($record) => $record->phone),
                Placeholder::make('birth_place')
                    ->label('Tempat Lahir')
                    ->content(fn ($record) => $record->birth_place),
                Placeholder::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->content(fn ($record) => $record->birth_date->format('d M Y')),
                Placeholder::make('address')
                    ->label('Alamat Domisili')
                    ->content(fn ($record) => $record->address),

                // Status Lamaran
                Placeholder::make('status')
                    ->label('Status')
                    ->content(fn ($record) => ucfirst($record->status)),

                // Daftar Dokumen yang Diminta
                Placeholder::make('required_documents')
                    ->label('Dokumen yang Diminta')
                    ->content(fn ($record) => is_array($record->required_documents) && count($record->required_documents)
                        ? implode(', ', $record->required_documents)
                        : '-'
                    ),

                // Proses Seleksi (jika ada)
                Placeholder::make('selection_process')
                    ->label('Proses Seleksi')
                    ->content(fn ($record) => $record->selection_process ?? '-'),

                // Tautan Berkas Unggahan
                Html::make('documents_links')
                ->label('Berkas Unggahan')
                ->html(function ($record): string {
                    // Jika tidak ada array documents atau kosong
                    if (! is_array($record->documents) || empty($record->documents)) {
                        return '<span>-</span>';
                    }

                    // Buat list berkas
                    $items = collect($record->documents)
                        ->map(function (string $path, string $key): string {
                            $url   = Storage::url($path);
                            $label = ucwords(str_replace('_', ' ', $key));

                            return sprintf(
                                '<li><a href="%s" target="_blank" class="text-blue-600 hover:underline">%s</a></li>',
                                $url,
                                $label,
                            );
                        })
                        ->implode('');

                    return "<ul class=\"list-disc ml-6\">{$items}</ul>";
                }),

            ]),
        ];
    }
}
