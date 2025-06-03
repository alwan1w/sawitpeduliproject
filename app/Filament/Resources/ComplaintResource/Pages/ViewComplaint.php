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
    public $new_image;

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
        if (empty($this->new_message) && empty($this->new_image)) {
            Notification::make()->title('Pesan tidak boleh kosong!')->danger()->send();
            return;
        }

        $imagePath = null;
        if ($this->new_image) {
            $imagePath = $this->new_image->store('complaint_images', 'public');
        }

        ComplaintMessage::create([
            'complaint_id' => $complaint->id,
            'sender_type'  => 'worker', // atau role login
            'sender_id'    => Auth::id(),
            'message'      => $this->new_message,
            'image'        => $imagePath,
        ]);

        $this->new_message = '';
        $this->new_image = null;
        Notification::make()->title('Pesan terkirim!')->success()->send();
    }
}


