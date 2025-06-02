<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sawit Peduli Career</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- HERO SECTION --}}
    <section class="relative bg-green-900 py-40 mb-10 overflow-hidden">
        <div class="absolute inset-0">
            <img src="{{ asset('https://www.astra-agro.co.id/wp-content/uploads/2024/08/petani-1600x800.jpeg') }}"
                alt="Career"
                class="w-full h-full object-cover opacity-30 md:opacity-40">
            <div class=></div> <!-- Overlay -->
        </div>

        <div class="relative z-10 max-w-3xl mx-auto text-center px-4">
            <h1 class="text-4xl md:text-5xl font-bold text-white mb-0 drop-shadow">Selamat Datang di <span class="text-yellow-300">Sawit Peduli Career</span></h1>
            <p class="text-lg md:text-xl text-white mb-6">{{ $info }}</p>
        </div>
    </section>

    {{-- LOWONGAN SECTION --}}
    <section class="max-w-5xl mx-auto px-4">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Lowongan Terbaru</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8">
            @forelse ($lowongans as $l)
                <div class="bg-white rounded-xl shadow hover:shadow-lg transition flex flex-col border border-gray-100">
                    <div class="px-4 pt-4">
                        <div class="text-lg font-semibold text-green-800 mb-1">{{ $l->position }}</div>
                        <div class="text-sm text-gray-500 mb-1">
                            @if($l->company)
                                <span class="inline-block"><svg class="w-4 h-4 inline mr-1 text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 21v-7a2 2 0 012-2h4V5a2 2 0 012-2h0a2 2 0 012 2v7h4a2 2 0 012 2v7"></path></svg>{{ $l->company->name }}</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-400 mb-2">Tutup: {{ \Illuminate\Support\Carbon::parse($l->close_date)->format('d M Y') }}</div>
                    </div>
                    <div class="flex-1 px-4 pb-4">
                        <p class="text-gray-700 text-sm mb-3">{{ \Illuminate\Support\Str::limit($l->detail_posisi, 80) }}</p>
                        <a href="#" class="text-sm font-semibold text-green-700 hover:underline">Lihat Detail</a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center text-gray-400 py-8">Belum ada lowongan terbuka.</div>
            @endforelse
        </div>
    </section>

    <div class="py-10"></div>

    {{-- Footer --}}
    <footer class="text-center text-xs text-gray-400 pb-8 mt-10">&copy; {{ date('Y') }} Sawit Peduli Career. All rights reserved.</footer>

</body>
</html>
