<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\ClinicBranch;
use App\Models\DoctorProfile;
use App\Models\DoctorWorkingHour;
use App\Models\Role;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        $doctorRole = Role::where('name', 'doctor')->firstOrFail();

        $clinic = Clinic::first();
        if (!$clinic) {
            $clinic = Clinic::create([
                'name' => 'MediConnect Clinic',
                'address' => 'TP.HCM',
            ]);
        }

        $branch = ClinicBranch::first();
        if (!$branch) {
            $branch = ClinicBranch::create([
                'clinic_id' => $clinic->id,
                'name' => 'Chi nhánh trung tâm',
                'address' => 'Q.1, TP.HCM',
                'phone' => '0900000000',
            ]);
        }

        $specialties = Specialty::all();
        if ($specialties->count() < 3) {
            $this->command->warn("⚠️ Not enough specialties. Please seed specialties first.");
            return;
        }

        $doctors = [
            [
                'name' => 'Nguyễn Trần Xuân Cường',
                'email' => 'doctor1@mediconnect.local',
                'specialty_id' => $specialties[0]->id,
                'avatar_url' => 'https://images.pexels.com/photos/6749768/pexels-photo-6749768.jpeg?auto=compress&cs=tinysrgb&w=400',
                'experience_years' => 9,
                'fee' => 2500000,
            ],
            [
                'name' => 'BS. Nguyễn Bảo Trân',
                'email' => 'doctor2@mediconnect.local',
                'specialty_id' => $specialties[1]->id,
                'avatar_url' => 'https://images.pexels.com/photos/6749768/pexels-photo-6749768.jpeg?auto=compress&cs=tinysrgb&w=400',
                'experience_years' => 7,
                'fee' => 1500000,
            ],
            [
                'name' => 'BS. Nguyễn Thảo Trang',
                'email' => 'doctor3@mediconnect.local',
                'specialty_id' => $specialties[2]->id,
                'avatar_url' => 'https://images.pexels.com/photos/6749768/pexels-photo-6749768.jpeg?auto=compress&cs=tinysrgb&w=400',
                'experience_years' => 10,
                'fee' => 1200000,
            ],
            [
                'name' => 'BS. Phạm Thùy Linh',
                'email' => 'doctor4@mediconnect.local',
                'specialty_id' => $specialties[0]->id,
                'avatar_url' => 'https://images.pexels.com/photos/7580252/pexels-photo-7580252.jpeg?auto=compress&cs=tinysrgb&w=400',
                'experience_years' => 6,
                'fee' => 1100000,
            ],
            [
                'name' => 'BS. Đặng Quang Vinh',
                'email' => 'doctor5@mediconnect.local',
                'specialty_id' => $specialties[1]->id,
                'avatar_url' => 'https://images.pexels.com/photos/6749768/pexels-photo-6749768.jpeg?auto=compress&cs=tinysrgb&w=400',
                'experience_years' => 13,
                'fee' => 899000,
            ],
            [
                'name' => 'BS. Võ Thanh Tùng',
                'email' => 'doctor6@mediconnect.local',
                'specialty_id' => $specialties[2]->id,
                'avatar_url' => 'https://images.pexels.com/photos/8460171/pexels-photo-8460171.jpeg?auto=compress&cs=tinysrgb&w=400',
                'experience_years' => 11,
                'fee' => 699000,
            ],
        ];

        foreach ($doctors as $doc) {

            $user = User::updateOrCreate(
                ['email' => $doc['email']],
                [
                    'name' => $doc['name'],
                    'password' => Hash::make('123456'),
                    'role_id' => $doctorRole->id,
                    'avatar_url' => $doc['avatar_url'],
                ]
            );

            $profile = DoctorProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'specialty_id' => $doc['specialty_id'],
                    'experience_years' => $doc['experience_years'],
                    'consultation_fee' => $doc['fee'],
                    'bio' => 'Bác sĩ có nhiều năm kinh nghiệm và tận tâm với bệnh nhân.',
                    'rating_avg' => 0,
                    'total_reviews' => 0,
                ]
            );

            foreach ([0,1,2,3,4,5,6] as $dow) {
                DoctorWorkingHour::updateOrCreate(
                    [
                        'doctor_profile_id' => $profile->id,
                        'day_of_week' => $dow,
                    ],
                    [
                        'clinic_branch_id' => $branch->id,
                        'start_time' => '08:00:00',
                        'end_time' => '17:00:00',
                    ]
                );
            }
        }

        $this->command->info("✅ Seed doctors + working hours done.");
    }
}
