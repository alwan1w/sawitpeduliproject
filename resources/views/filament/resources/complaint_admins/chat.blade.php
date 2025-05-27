<div>
    @php
        $messages = $this->record->messages()->orderBy('created_at')->get();
    @endphp

    <div style="height:400px; overflow-y:auto; background: var(--tw-bg-opacity, 1) #f4f8fb; padding: 1rem; border-radius:8px; margin-bottom:1rem;">
        @foreach ($messages as $msg)
            @php
                $isWorker = $msg->sender_type === 'worker';
                $bubbleBg = $isWorker ? '#2563eb' : '#16a34a'; // biru (pekerja), hijau (pemkab)
                $align = $isWorker ? 'flex-start' : 'flex-end';
                $name = $isWorker ? ($msg->sender->name ?? 'Pekerja') : 'Pemkab';
                $margin = $isWorker ? 'margin-right:auto;' : 'margin-left:auto;';
            @endphp
            <div style="margin-bottom: 16px; display: flex; {{ $align === 'flex-start' ? 'justify-content: flex-start;' : 'justify-content: flex-end;' }}">
                <div style="display:block; width:100%;">
                    <div style="font-size:12px; font-weight:bold; color:{{ $isWorker ? '#2563eb' : '#16a34a' }}; margin-bottom:2px; {{ $isWorker ? '' : 'text-align:right;' }}">
                        {{ $name }}
                    </div>
                    <div style="max-width:65%; min-width:80px; background:{{ $bubbleBg }}; color:#fff; padding:12px 16px; border-radius:16px; font-size:1rem; {{ $margin }}">
                        {{ $msg->message }}
                        <div style="font-size:11px; color:#ffffff; text-align:right; margin-top:6px;">
                            {{ $msg->created_at->timezone('Asia/Jakarta')->format('H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <form wire:submit.prevent="sendMessage" style="display: flex; align-items: center;">
        <textarea wire:model.defer="new_message"
            placeholder="Tulis pesan..."
            rows="2"
            style="width:80%; color: #111; background: #fff; border-radius:5px; padding:8px; margin-right:1%;"
        ></textarea>
        <button type="submit"
            style="width:18%; background:#2563eb; color:white; border:none; border-radius:5px;">
            Kirim
        </button>
    </form>
</div>
