<?php

namespace App\Http\Controllers;

use App\Models\Info;

class LandingController extends Controller
{
    public function welcome()
    {
        $infos = Info::latest()->take(6)->get();
        return view('welcome', compact('infos'));
    }
}
