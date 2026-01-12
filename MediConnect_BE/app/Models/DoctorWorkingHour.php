<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorWorkingHour extends Model
{
    use HasFactory;

    protected $table = 'doctor_working_hours';

    protected $fillable = [
    'doctor_profile_id',
    'clinic_branch_id',
    'day_of_week',
    'start_time',
    'end_time',
];


    protected $casts = [
        'day_of_week' => 'integer',
        'slot_duration_minutes' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relations
    public function doctorProfile()
    {
        return $this->belongsTo(DoctorProfile::class);
    }

    public function clinicBranch()
    {
        return $this->belongsTo(ClinicBranch::class);
    }
}
