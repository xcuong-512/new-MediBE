<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSlot extends Model
{
    protected $fillable = [
        'doctor_profile_id',
        'clinic_branch_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'generated_from_working_hour_id',
    ];

    public function doctor()
    {
        return $this->belongsTo(DoctorProfile::class, 'doctor_profile_id');
    }
}
