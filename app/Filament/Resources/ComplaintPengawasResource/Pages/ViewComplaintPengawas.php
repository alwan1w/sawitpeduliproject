<?php

namespace App\Filament\Resources\ComplaintPengawasResource\Pages;

use Filament\Resources\Pages\ViewRecord;
use App\Models\ComplaintMessage;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ViewComplaintPengawas extends ViewRecord
{
    protected static string $resource = \App\Filament\Resources\ComplaintPengawasResource::class;

    public $new_message = '';
    public $new_image;

    public static string $view = 'filament.resources.complaint_pengawas.chat';

    public function mount($record): void
    {
        parent::mount($record);
        $this->new_message = '';
    }

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
            'sender_type'  => 'pengawas',
            'sender_id'    => Auth::id(),
            'message'      => $this->new_message,
            'image'        => $imagePath,
        ]);
        $this->new_message = '';
        $this->new_image = null;
        Notification::make()->title('Pesan terkirim!')->success()->send();
    }
}
