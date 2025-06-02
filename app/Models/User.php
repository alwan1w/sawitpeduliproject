<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;


/**
 * @method \Illuminate\Database\Eloquent\Relations\BelongsToMany trainings()
 * @method bool hasCertification(int|string $sertifikasiId)
 */

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function legalitas()
    {
        return $this->hasOne(Legalitas::class, 'agency_id');
    }

    public function applications()
    {
        return $this->hasMany(\App\Models\Application::class, 'user_id');
    }

    /**
     * Relasi dengan model Training melalui tabel pivot training_participants.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'training_participants', 'user_id', 'training_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    /**
     * Mengecek apakah user memiliki sertifikasi dengan ID tertentu.
     *
     * @param int|string $sertifikasiId
     * @return bool
     */
    public function hasCertification($sertifikasiId)
    {
        return $this->trainings()
            ->where('sertifikasi_id', $sertifikasiId)
            ->wherePivot('status', 'kompeten')
            ->exists();
    }

}
