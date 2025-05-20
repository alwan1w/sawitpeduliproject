<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'company_id',
        'recruitment_id',
        'start_date',
        'user_id',
        'batas_kontrak',
        'status_kontrak',
    ];

    protected $casts = [
        'start_date'     => 'date',
        'batas_kontrak'  => 'date',
    ];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function recruitment()
    {
        return $this->belongsTo(Recruitment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
