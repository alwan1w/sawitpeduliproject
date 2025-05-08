<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pelamar</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-bold mb-4 text-center">Registrasi Pelamar</h2>

        @if ($errors->any())
            <div class="mb-4 text-red-600 text-sm">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-4">
                <label class="block mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full border px-3 py-2 rounded" />
            </div>

            <div class="mb-4">
                <label class="block mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required class="w-full border px-3 py-2 rounded" />
            </div>

            <div class="mb-4">
                <label class="block mb-1">Password</label>
                <input type="password" name="password" required class="w-full border px-3 py-2 rounded" />
            </div>

            <div class="mb-6">
                <label class="block mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required class="w-full border px-3 py-2 rounded" />
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded">
                Daftar
            </button>
        </form>
    </div>
</body>
</html>
