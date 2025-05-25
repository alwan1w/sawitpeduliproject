<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'tema_pelatihan',
        'moderator',
        'tanggal_pelatihan',
        'materi_id',
        'sertifikasi_id',
        'kuota_peserta',
        'lokasi',
    ];

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }

    public function sertifikasi()
    {
        return $this->belongsTo(\App\Models\Sertifikasi::class);
    }

    public function participants()
    {
        return $this->hasMany(TrainingParticipant::class);
    }
}
