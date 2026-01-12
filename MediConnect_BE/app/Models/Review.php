<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_profile_id',
        'appointment_id',
        'rating',
        'comment',
    ];
    public function patient()
    {
        return $this->belongsTo(\App\Models\User::class, 'patient_id');
    }
}
