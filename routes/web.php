<?php

use App\Models\Info;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Models\Recruitment;

Route::get('/', function () {
    $info = "Temukan peluang karir terbaik di industri sawit. Sawit Peduli siap menghubungkan Anda dengan perusahaan terbaik!"; // Atau ambil dari model/info kalau mau dinamis
    $lowongans = Recruitment::where('status', 'mencari_pekerja')->latest()->take(6)->get();
    return view('welcome', compact('info', 'lowongans'));
});


Route::get('/register', [RegisterController::class, 'show'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);
