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

}
