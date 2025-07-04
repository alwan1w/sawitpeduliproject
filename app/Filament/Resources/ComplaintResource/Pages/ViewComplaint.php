<?php

namespace App\Filament\Resources\ComplaintResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use App\Models\ComplaintMessage;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ViewComplaint extends ViewRecord
{
    protected static string $resource = \App\Filament\Resources\ComplaintResource::class;

    // Simpan pesan baru
    public $new_message = '';
    public $new_file;

    // Pakai custom blade view
    public static string $view = 'filament.resources.complaints.chat';

    public function mount($record): void
    {
        parent::mount($record);
        $this->new_message = '';
    }

    // Fungsi kirim pesan
    public function sendMessage()
    {
        $complaint = $this->record;

        if (empty($this->new_message) && empty($this->new_file)) {
            Notification::make()->title('Pesan tidak boleh kosong!')->danger()->send();
            return;
        }

        $filePath = null;
        $originalName = null;
        if ($this->new_file) {
            $originalName = $this->new_file->getClientOriginalName();
            $filename = time() . '_' . $originalName;
            $filePath = $this->new_file->storeAs('complaint_files', $filename, 'public');
        }

        ComplaintMessage::create([
            'complaint_id' => $complaint->id,
            'sender_type'  => 'worker',
            'sender_id'    => Auth::id(),
            'message'      => $this->new_message,
            'image'        => $filePath, // rename kolom image menjadi lebih umum misal file_path jika memungkinkan
        ]);

        $this->new_message = '';
        $this->new_file = null;

        Notification::make()->title('Pesan terkirim!')->success()->send();
    }
}


