<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'appointment_code',
        'patient_id',
        'doctor_profile_id',
        'clinic_branch_id',
        'date',
        'start_time',
        'end_time',
        'type',
        'status',
        'symptom_note',
        'doctor_note',
    ];
    
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctorProfile()
    {
        return $this->belongsTo(DoctorProfile::class, 'doctor_profile_id');
    }

    public function clinicBranch()
    {
        return $this->belongsTo(ClinicBranch::class, 'clinic_branch_id');
    }

}
