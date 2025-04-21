<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recruitment extends Model
{
    use HasFactory;

    protected $fillable = [
        'position',
        'detail_posisi',
        'requirement_total',
        'open_date',
        'close_date',
        'salary_range',
        'contract_duration',
        'skills',
        'age_range',
        'education',
        'status',
        'company_id',
        'agency_id',
    ];

    public function company()
    {
        return $this->belongsTo(User::class, 'company_id');
    }

    public function agency()
    {
        return $this->belongsTo(User::class, 'agency_id');
    }
    public function applications()
    {
        return $this->hasMany(\App\Models\Application::class);
    }

}
