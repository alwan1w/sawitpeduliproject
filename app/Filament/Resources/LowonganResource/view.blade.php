<x-filament::page>
    <x-filament::card>
        <h2 class="text-xl font-bold mb-4">Detail Lowongan</h2>

        <p><strong>Perusahaan:</strong> {{ $record->company->name ?? '-' }}</p>
        <p><strong>Posisi:</strong> {{ $record->position }}</p>
        <p><strong>Detail Posisi:</strong> {{ $record->detail_posisi }}</p>
        <p><strong>Gaji:</strong> {{ $record->salary_range }}</p>
        <p><strong>Durasi Kontrak:</strong> {{ $record->contract_duration }}</p>
        <p><strong>Keahlian:</strong> {{ $record->skills }}</p>
        <p><strong>Rentang Usia:</strong> {{ $record->age_range }}</p>
        <p><strong>Pendidikan Minimal:</strong> {{ $record->education }}</p>
        <p><strong>Dokumen yang Diperlukan:</strong>
            {{ implode(', ', $record->required_documents ?? []) }}
        </p>
        <p><strong>Proses Seleksi:</strong> {{ $record->selection_process }}</p>
        <p><strong>Tanggal Dibuka:</strong>
            {{ \Carbon\Carbon::parse($record->open_date)->format('d M Y') }}
        </p>
        <p><strong>Tanggal Ditutup:</strong>
            {{ \Carbon\Carbon::parse($record->close_date)->format('d M Y') }}
        </p>
    </x-filament::card>
</x-filament::page>
