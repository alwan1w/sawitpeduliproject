<div>
    @php
        $messages = $this->record->messages()->orderBy('created_at')->get();
    @endphp

    <div style="height:400px; overflow-y:auto; background:var(--tw-bg-opacity, 1) #f4f8fb; padding: 1rem; border-radius:8px; margin-bottom:1rem;">
        @foreach ($messages as $msg)
           @php
                if ($msg->sender_type === 'worker') {
                    $bubbleBg = '#2563eb'; // biru
                    $align = 'flex-start'; // kiri
                    $name = $msg->sender->name ?? 'Pekerja';
                    $margin = 'margin-right:auto;';
                    $nameAlign = '';
                } elseif ($msg->sender_type === 'pemkab') {
                    $bubbleBg = '#16a34a'; // hijau
                    $align = 'flex-start'; // kiri
                    $name = 'Pemkab';
                    $margin = 'margin-right:auto;';
                    $nameAlign = '';
                } elseif ($msg->sender_type === 'pengawas') {
                    $bubbleBg = '#f59e42'; // oranye
                    $align = 'flex-end'; // kanan
                    $name = $msg->sender->name ?? 'Pengawas';
                    $margin = 'margin-left:auto;';
                    $nameAlign = 'text-align:right;';
                } else {
                    $bubbleBg = '#666'; // fallback abu
                    $align = 'flex-start';
                    $name = 'Unknown';
                    $margin = 'margin-right:auto;';
                    $nameAlign = '';
                }
            @endphp
            <div style="margin-bottom: 16px; display: flex; justify-content: {{ $align }};">
                <div style="display:block; width:100%;">
                    <div style="font-size:12px; font-weight:bold; color:{{ $bubbleBg }}; margin-bottom:2px; {{ $nameAlign }}">
                        {{ $name }}
                    </div>
                    <div style="max-width:65%; min-width:80px; background:{{ $bubbleBg }}; color:#fff; padding:12px 16px; border-radius:16px; font-size:1rem; {{ $margin }}">
                        @if($msg->image)
                            <a href="{{ Storage::url($msg->image) }}" target="_blank">
                                <img src="{{ Storage::url($msg->image) }}" alt="image" style="max-width:150px; border-radius:8px; margin-bottom:6px;" />
                            </a>
                        @endif
                        {{ $msg->message }}
                        <div style="font-size:11px; color:#ffffff; text-align:right; margin-top:6px;">
                            {{ $msg->created_at->timezone('Asia/Jakarta')->format('H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <form wire:submit.prevent="sendMessage" style="display: flex; align-items: center; gap:8px;">
            <textarea wire:model.defer="new_message"
                placeholder="Tulis pesan..."
                rows="2"
                style="width:65%; color: #111; background: #fff; border-radius:5px; padding:8px;"
            ></textarea>
            <input type="file" wire:model="new_image" accept="image/*" style="width:20%;" />
            <button type="submit"
                style="width:15%; background:#2563eb; color:white; border:none; border-radius:5px;">
                Kirim
            </button>
    </form>
</div>
