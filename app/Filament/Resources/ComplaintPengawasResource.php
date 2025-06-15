<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Complaint;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\ComplaintPengawas;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ComplaintPengawasResource\Pages;
use App\Filament\Resources\ComplaintPengawasResource\RelationManagers;

class ComplaintPengawasResource extends Resource
{
    protected static ?string $model = Complaint::class;
    protected static ?string $navigationLabel = 'Pengaduan Masuk';
    protected static ?string $navigationGroup = 'Pengawas';
    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    public static function form(Form $form): Form
    {
        // Biasanya Pemkab tidak create pengaduan, jadi biarkan kosong atau readonly.
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
                Tables\Columns\TextColumn::make('subject')->label('Laporan'),
                Tables\Columns\TextColumn::make('worker.name')->label('Pelapor'),
                Tables\Columns\BadgeColumn::make('status')
                ->colors([
                    'warning' => 'diajukan',
                    'info'    => 'diproses',
                    'success' => 'selesai',
                ])
                ->label('Status'),
                Tables\Columns\TextColumn::make('created_at')->label('Waktu')->dateTime('d M Y H:i'),
            ])
            ->actions([
                Tables\Actions\Action::make('detail_pelapor')
                    ->icon('heroicon-o-identification')
                    ->color('info')
                    ->label('')
                    ->modalHeading('Detail Pelapor')
                    ->modalContent(function ($record) {
                        $worker = $record->worker;
                        $application = $worker->applications()->latest()->first();
                        $position = $application->recruitment->position ?? '-';
                        $company = $application->recruitment->company->name ?? '-';
                        $phone = $application->phone ?? '-';

                        return new HtmlString("
                            <div class='grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4'>
                                <div class='font-semibold text-gray-700 dark:text-gray-200 flex items-center'>Nama</div>
                                <div class='bg-gray-100 dark:bg-gray-800 rounded px-4 py-2 text-gray-900 dark:text-gray-100 flex items-center'>{$worker->name}</div>

                                <div class='font-semibold text-gray-700 dark:text-gray-200 flex items-center'>Posisi Kerja</div>
                                <div class='bg-gray-100 dark:bg-gray-800 rounded px-4 py-2 text-gray-900 dark:text-gray-100 flex items-center'>{$position}</div>

                                <div class='font-semibold text-gray-700 dark:text-gray-200 flex items-center'>No. Ponsel</div>
                                <div class='bg-gray-100 dark:bg-gray-800 rounded px-4 py-2 text-gray-900 dark:text-gray-100 flex items-center'>{$phone}</div>

                                <div class='font-semibold text-gray-700 dark:text-gray-200 flex items-center'>Nama Perusahaan</div>
                                <div class='bg-gray-100 dark:bg-gray-800 rounded px-4 py-2 text-gray-900 dark:text-gray-100 flex items-center'>{$company}</div>
                            </div>
                        ");
                    })
                    ->modalWidth('md')
                    ->requiresConfirmation(false)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Tutup'),

                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->label('')
                    ->color('primary')
                    ->url(fn($record) => static::getUrl('view', ['record' => $record])),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->label(''),
                Tables\Actions\Action::make('set_proses')
                    ->label('Proses')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->visible(fn($record) => $record->status === 'diajukan')
                    ->action(fn($record) => $record->update(['status' => 'diproses'])),
                Tables\Actions\Action::make('set_selesai')
                    ->label('Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn($record) => $record->status === 'diproses')
                    ->action(fn($record) => $record->update(['status' => 'selesai'])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaintPengawas::route('/'),
            'create' => Pages\CreateComplaintPengawas::route('/create'),
            'edit' => Pages\EditComplaintPengawas::route('/{record}/edit'),
            'view' => Pages\ViewComplaintPengawas::route('/{record}'),
        ];
    }

    public static function canAccess(): bool
    {
        return Gate::allows('pengaduan masuk pengawas');
    }
}
