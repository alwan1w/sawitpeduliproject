<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Selection extends Model
{
    use HasFactory;

    protected $fillable = ['application_id', 'status'];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }
}

