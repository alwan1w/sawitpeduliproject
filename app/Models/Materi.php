<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul_materi',
        'file',
        'tujuan',
        'deskripsi',
        'isi_materi',
    ];

    public function trainings()
    {
        return $this->hasMany(Training::class, 'materi_id');
    }
}
