<?php

namespace App\Filament\Resources\RecruitmentResource\Pages;

use App\Filament\Resources\RecruitmentResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\CheckboxList;

class ViewRecruitment extends ViewRecord
{
    protected static string $resource = RecruitmentResource::class;

    // Override form schema supaya data muncul
    protected function getFormSchema(): array
    {
        return [
            Card::make()->schema([
                TextInput::make('company.name')
                    ->label('Perusahaan')
                    ->disabled(),

                TextInput::make('position')
                    ->label('Posisi')
                    ->disabled(),

                Textarea::make('detail_posisi')
                    ->label('Detail Posisi')
                    ->rows(4)
                    ->disabled(),

                TextInput::make('salary_range')
                    ->label('Rentang Gaji')
                    ->disabled(),

                TextInput::make('contract_duration')
                    ->label('Durasi Kontrak')
                    ->disabled(),

                Textarea::make('skills')
                    ->label('Keahlian Diperlukan')
                    ->rows(3)
                    ->disabled(),

                TextInput::make('age_range')
                    ->label('Rentang Usia')
                    ->disabled(),

                TextInput::make('education')
                    ->label('Pendidikan Minimal')
                    ->disabled(),

                CheckboxList::make('required_documents')
                    ->label('Dokumen yang Diperlukan')
                    ->options(array_combine(
                        $this->record->required_documents,
                        $this->record->required_documents,
                    ))
                    ->columns(2)
                    ->disabled(),

                Textarea::make('selection_process')
                    ->label('Proses Seleksi')
                    ->rows(4)
                    ->disabled(),

                DatePicker::make('open_date')
                    ->label('Tanggal Dibuka')
                    ->disabled(),

                DatePicker::make('close_date')
                    ->label('Tanggal Ditutup')
                    ->disabled(),
            ]),
        ];
    }
}
