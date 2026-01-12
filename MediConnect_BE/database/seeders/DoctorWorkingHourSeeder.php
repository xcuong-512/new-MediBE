<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DoctorProfile;
use App\Models\DoctorWorkingHour;
use App\Models\ClinicBranch;

class DoctorWorkingHourSeeder extends Seeder
{
    public function run(): void
    {
        $branches = ClinicBranch::all();
        $doctors = DoctorProfile::all();

        foreach ($doctors as $doctor) {
            // Mon-Fri (1-5), 8h-12h & 13h-17h
            foreach ([1,2,3,4,5] as $dow) {
                DoctorWorkingHour::firstOrCreate([
                    'doctor_profile_id' => $doctor->id,
                    'clinic_branch_id' => $branches->random()->id,
                    'day_of_week' => $dow,
                    'start_time' => '08:00:00',
                    'end_time' => '12:00:00',
                ], [
                    'slot_minutes' => 30,
                ]);

                DoctorWorkingHour::firstOrCreate([
                    'doctor_profile_id' => $doctor->id,
                    'clinic_branch_id' => $branches->random()->id,
                    'day_of_week' => $dow,
                    'start_time' => '13:00:00',
                    'end_time' => '17:00:00',
                ], [
                    'slot_minutes' => 30,
                ]);
            }
        }
    }
}
