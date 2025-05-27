<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = ['worker_id', 'subject', 'status'];

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id'); // sesuaikan jika pakai Worker model
    }

    public function messages()
    {
        return $this->hasMany(ComplaintMessage::class);
    }
}
