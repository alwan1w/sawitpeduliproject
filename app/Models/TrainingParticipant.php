<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TrainingParticipant extends Model
{

    protected $table = 'training_participants';

    protected $fillable = [
        'training_id', 'user_id', 'nama', 'alamat', 'tempat_lahir', 'tanggal_lahir', 'gender', 'no_ponsel', 'status'
    ];

    public function training()
    {
        return $this->belongsTo(Training::class,'training_id','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

