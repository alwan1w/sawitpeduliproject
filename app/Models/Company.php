<?php

// app/Models/Company.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'director',
        'phone',
        'email',
        'address',
        'nib',
        'tdp',
        'akta',
        'suip',
        'npwp',
        'izin_operasional',
    ];

    public function recruitments()
    {
        return $this->hasMany(Recruitment::class);
    }

    public function workers()
    {
        return $this->hasMany(Worker::class);
    }
}

