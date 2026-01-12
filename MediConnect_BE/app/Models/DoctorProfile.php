<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorProfile extends Model
{
    protected $fillable = [
        'user_id',
        'specialty_id',
        'license_number',
        'bio',
        'experience_years',
        'consultation_fee',
        'rating_avg',
        'total_reviews',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function specialty()
    {
        return $this->belongsTo(Specialty::class);
    }
}
