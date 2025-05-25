<?php
namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables\Table;
use App\Models\Recruitment;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Gate;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\Action as TableAction;
use App\Filament\Resources\LowonganResource\Pages\ViewLowongan;
use App\Filament\Resources\LowonganResource\Pages\ListLowongans;

class LowonganResource extends Resource
{
    protected static ?string $model           = Recruitment::class;
    protected static ?string $navigationGroup = 'Pelamar';
    protected static ?string $navigationLabel = 'Lowongan Kerja';
    protected static ?string $navigationIcon  = 'heroicon-o-briefcase';
    // protected static string $slug              = 'lowongans'; // ganti URL segmennya

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')->label('Perusahaan')->sortable()->searchable(),
                TextColumn::make('position')->label('Posisi')->sortable()->searchable(),
                TextColumn::make('requirement_total')->label('Jumlah Dibutuhkan'),
                TextColumn::make('close_date')->label('Tutup')->date('d M Y'),
            ])
            ->actions([
                ViewAction::make(),
                TableAction::make('lamar')
                    ->label('Lamar')
                    ->icon('heroicon-o-document-text')
                    ->url(fn (Recruitment $r) => \App\Filament\Resources\ApplicationResource::getUrl('create', [
                        'recruitment_id' => $r->id,
                    ]))
                    ->color('primary'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLowongans::route('/'),
            'view'  => ViewLowongan::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('status', 'mencari_pekerja')
            ->whereDate('close_date', '>=', today());
    }

     public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            TextEntry::make('company.name')->label('Perusahaan'),
            TextEntry::make('position')->label('Posisi'),
            TextEntry::make('detail_posisi')->label('Detail Posisi'),
            TextEntry::make('salary_range')->label('Rentang Gaji'),
            TextEntry::make('contract_duration')->label('Durasi Kontrak'),
            TextEntry::make('skills')->label('Keahlian'),
            TextEntry::make('age_range')->label('Rentang Usia'),
            TextEntry::make('education')->label('Pendidikan Minimal'),

            TextEntry::make('required_documents')
                ->label('Dokumen yang Diperlukan')
                ->formatStateUsing(fn ($state): string =>
                    is_array($state) && count($state)
                        ? implode(', ', $state)
                        : $state
                ),

            TextEntry::make('selection_process')->label('Proses Seleksi'),

            TextEntry::make('open_date')
                ->label('Tanggal Dibuka')
                ->formatStateUsing(fn ($state) => $state ? $state->format('d M Y') : '—'),

            TextEntry::make('close_date')
                ->label('Tanggal Ditutup')
                ->formatStateUsing(fn ($state) => $state ? $state->format('d M Y') : '—'),
        ]);
    }

    public static function canAccess(): bool
    {
        return Gate::allows('akses lowongan');
    }
}
