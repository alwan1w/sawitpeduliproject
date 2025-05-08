<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'recruitment_id',
        'user_id',
        'name',
        'phone',
        'birth_place',
        'birth_date',
        'address',
        'documents',
        // field upload dinamis: slug dokumen akan otomatis jadi kolom di migration
        'status',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'required_documents' => 'array',
        'documents'          => 'array',
    ];

    public function recruitment()
    {
        return $this->belongsTo(Recruitment::class);
    }

    public function selection()
    {
        return $this->hasOne(\App\Models\Selection::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
