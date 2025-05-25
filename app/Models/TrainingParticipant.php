<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingParticipant extends Model
{
    protected $fillable = [
        'training_id', 'user_id', 'nama', 'alamat', 'tempat_lahir', 'tanggal_lahir', 'gender', 'no_ponsel', 'status'
    ];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

