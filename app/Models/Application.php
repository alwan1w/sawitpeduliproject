<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'recruitment_id',
        'name',
        'phone',
        'birth_place',
        'birth_date',
        'address',
        'cv',
        'certificate',
        'ijazah',
        'status',
    ];

    public function recruitment()
    {
        return $this->belongsTo(Recruitment::class);
    }

    public function selection()
    {
        return $this->hasOne(\App\Models\Selection::class);
    }
}
