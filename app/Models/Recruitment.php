<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Recruitment extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
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
        'agency_id',
        'required_documents',
        'selection_process',
        'agency_status',
    ];

    protected $casts = [
        'required_documents' => 'array',
        'documents'          => 'array',
        'selection_process'  => 'string',   // optional
        'open_date'          => 'date',
        'close_date'         => 'date',
    ];



    // Relationship with Agency (user model for agencies)
    public function agency()
    {
        return $this->belongsTo(User::class, 'agency_id');
    }

    // Relationship with workers (no company_id needed)
    public function workers()
    {
        return $this->hasMany(\App\Models\Worker::class);
    }

    // Relationship with applications
    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeOpenForApplication(Builder $query): Builder
    {
        return $query
            ->where('status', 'mencari_pekerja')
            ->where('close_date', '>=', now());
    }

}
