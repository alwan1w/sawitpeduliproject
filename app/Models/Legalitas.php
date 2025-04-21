<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Legalitas extends Model
{
    use HasFactory;

    protected $fillable = [
        'agency_id',
        'nama_perusahaan',
        'alamat',
        'kontak',
        'email',
        'akta',
        'nib',
        'suip',
        'tdp',
        'npwp',
        'izin_operasional',
        'file_akta',
        'file_nib',
        'file_suip',
        'file_tdp',
        'file_npwp',
        'file_izin_operasional',
        'status',
    ];

    public function agency()
    {
        return $this->belongsTo(User::class, 'agency_id');
    }

    protected static function booted()
    {
        static::creating(function ($legalitas) {
            $legalitas->agency_id = $legalitas->agency_id ?? Auth::id();
        });
    }


}

