<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'tema_pelatihan',
        'moderator',
        'tanggal_pelatihan',
        'materi_id',
        'kuota_peserta',
        'lokasi',
    ];

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }
}
