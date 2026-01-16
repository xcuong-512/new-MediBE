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

        $avatars = [
            "https://cdn.bookingcare.vn/fo/w640/2025/08/14/152821-anh-bs-3.png",
            "https://cdn.bookingcare.vn/fo/w640/2025/06/23/092155-ts-pham-chi-lang.png",
            "https://cdn.bookingcare.vn/fo/w640/2025/06/20/161055-bs-nguyen-huu-thao-1.png",
            "https://cdn.bookingcare.vn/fo/w640/2025/03/18/142438-quynh-pgs.jpg",
            "https://cdn.bookingcare.vn/fo/w640/2025/06/12/104052-thac-si-bac-si-tran-thi-mai-thy.png",
            "https://cdn.bookingcare.vn/fo/w640/2025/08/14/152122-anh-bs.png",
            "https://cdn.bookingcare.vn/fo/w640/2025/03/18/142802-bs-thanh.png",
            "https://cdn.bookingcare.vn/fo/w640/2025/08/14/153243-anh-bs-5.png",
            "https://cdn.bookingcare.vn/fo/w640/2025/06/18/093038-bs-pham-tien-lang.png",
            "https://cdn.bookingcare.vn/fo/w640/2025/08/14/152557-anh-bs-2.png",
            "https://cdn.bookingcare.vn/fo/w640/2025/08/14/153759-anh-bs-7.png",
            "https://cdn.bookingcare.vn/fo/w640/2025/06/18/105310-gs-ha-van-quyet.png",
            "https://cdn.bookingcare.vn/fo/w640/2025/03/18/142152-bs-doanh.png",
            "https://cdn.bookingcare.vn/fo/w640/2025/08/14/152355-anh-bs-1.png",
            "https://cdn.bookingcare.vn/fo/w640/2025/08/14/152953-anh-bs-4.png",
            "https://i.pravatar.cc/300?img=31",
            "https://i.pravatar.cc/300?img=32",
            "https://i.pravatar.cc/300?img=33",
            "https://i.pravatar.cc/300?img=34",
            "https://i.pravatar.cc/300?img=35",
        ];

        $doctors = [
            [
                'name' => 'Giáo sư, Tiến sĩ, Bác sĩ Kiều Đình Hùng',
                'email' => 'doctor1@mediconnect.local',
                'specialty_id' => $specialties[0 % $specialties->count()]->id,
                'avatar_url' => $avatars[0],
                'experience_years' => 30,
                'fee' => 900000,
            ],
            [
                'name' => 'Tiến sĩ, Bác sĩ Phạm Chí Lăng',
                'email' => 'doctor2@mediconnect.local',
                'specialty_id' => $specialties[1 % $specialties->count()]->id,
                'avatar_url' => $avatars[1],
                'experience_years' => 18,
                'fee' => 700000,
            ],
            [
                'name' => 'Thạc sĩ, Bác sĩ Nội trú Nguyễn Hữu Thảo',
                'email' => 'doctor3@mediconnect.local',
                'specialty_id' => $specialties[2 % $specialties->count()]->id,
                'avatar_url' => $avatars[2],
                'experience_years' => 10,
                'fee' => 500000,
            ],
            [
                'name' => 'Đại tá, Phó giáo sư, Tiến sĩ, Bác sĩ CKII Nguyễn Văn Quýnh',
                'email' => 'doctor4@mediconnect.local',
                'specialty_id' => $specialties[0 % $specialties->count()]->id,
                'avatar_url' => $avatars[3],
                'experience_years' => 28,
                'fee' => 850000,
            ],
            [
                'name' => 'Thạc sĩ, Bác sĩ Trần Thị Mai Thy',
                'email' => 'doctor5@mediconnect.local',
                'specialty_id' => $specialties[1 % $specialties->count()]->id,
                'avatar_url' => $avatars[4],
                'experience_years' => 12,
                'fee' => 450000,
            ],
            [
                'name' => 'PGS.TS. Bác sĩ Nguyễn Trọng Hưng',
                'email' => 'doctor6@mediconnect.local',
                'specialty_id' => $specialties[2 % $specialties->count()]->id,
                'avatar_url' => $avatars[5],
                'experience_years' => 22,
                'fee' => 800000,
            ],
            [
                'name' => 'Thầy thuốc ưu tú, Phó giáo sư, Tiến sĩ Nguyễn Xuân Thành',
                'email' => 'doctor7@mediconnect.local',
                'specialty_id' => $specialties[0 % $specialties->count()]->id,
                'avatar_url' => $avatars[6],
                'experience_years' => 24,
                'fee' => 800000,
            ],
            [
                'name' => 'Bác sĩ Chuyên khoa II Võ Văn Mẫn',
                'email' => 'doctor8@mediconnect.local',
                'specialty_id' => $specialties[1 % $specialties->count()]->id,
                'avatar_url' => $avatars[7],
                'experience_years' => 15,
                'fee' => 600000,
            ],
            [
                'name' => 'Thầy thuốc Ưu tú, Bác sĩ CKII Nguyễn Tiến Lãng',
                'email' => 'doctor9@mediconnect.local',
                'specialty_id' => $specialties[2 % $specialties->count()]->id,
                'avatar_url' => $avatars[8],
                'experience_years' => 20,
                'fee' => 650000,
            ],
            [
                'name' => 'Bác sĩ Chuyên khoa II Lê Hồng Anh',
                'email' => 'doctor10@mediconnect.local',
                'specialty_id' => $specialties[0 % $specialties->count()]->id,
                'avatar_url' => $avatars[9],
                'experience_years' => 17,
                'fee' => 600000,
            ],
            [
                'name' => 'Phó Giáo sư, Tiến sĩ, Bác sĩ Nguyễn Thị Hoài An',
                'email' => 'doctor11@mediconnect.local',
                'specialty_id' => $specialties[1 % $specialties->count()]->id,
                'avatar_url' => $avatars[10],
                'experience_years' => 21,
                'fee' => 800000,
            ],
            [
                'name' => 'Giáo sư, Tiến sĩ Hà Văn Quyết',
                'email' => 'doctor12@mediconnect.local',
                'specialty_id' => $specialties[2 % $specialties->count()]->id,
                'avatar_url' => $avatars[11],
                'experience_years' => 32,
                'fee' => 900000,
            ],
            [
                'name' => 'Thầy thuốc ưu tú, Tiến sĩ, Bác sĩ Nguyễn Văn Doanh',
                'email' => 'doctor13@mediconnect.local',
                'specialty_id' => $specialties[0 % $specialties->count()]->id,
                'avatar_url' => $avatars[12],
                'experience_years' => 19,
                'fee' => 700000,
            ],
            [
                'name' => 'Tiến sĩ, Bác sĩ Chuyên khoa II Trà Anh Duy',
                'email' => 'doctor14@mediconnect.local',
                'specialty_id' => $specialties[1 % $specialties->count()]->id,
                'avatar_url' => $avatars[13],
                'experience_years' => 16,
                'fee' => 750000,
            ],
            [
                'name' => 'Tiến sĩ, Bác sĩ chuyên khoa II Lê Quốc Việt',
                'email' => 'doctor15@mediconnect.local',
                'specialty_id' => $specialties[2 % $specialties->count()]->id,
                'avatar_url' => $avatars[14],
                'experience_years' => 15,
                'fee' => 750000,
            ],

            [
                'name' => 'Bác sĩ CKI Nguyễn Hoàng Long',
                'email' => 'doctor16@mediconnect.local',
                'specialty_id' => $specialties[0 % $specialties->count()]->id,
                'avatar_url' => $avatars[15],
                'experience_years' => 9,
                'fee' => 400000,
            ],
            [
                'name' => 'Thạc sĩ, Bác sĩ Nguyễn Minh Khánh',
                'email' => 'doctor17@mediconnect.local',
                'specialty_id' => $specialties[1 % $specialties->count()]->id,
                'avatar_url' => $avatars[16],
                'experience_years' => 12,
                'fee' => 450000,
            ],
            [
                'name' => 'Bác sĩ CKII Trần Thị Thanh Hằng',
                'email' => 'doctor18@mediconnect.local',
                'specialty_id' => $specialties[2 % $specialties->count()]->id,
                'avatar_url' => $avatars[17],
                'experience_years' => 14,
                'fee' => 600000,
            ],
            [
                'name' => 'Tiến sĩ, Bác sĩ Phan Anh Tuấn',
                'email' => 'doctor19@mediconnect.local',
                'specialty_id' => $specialties[0 % $specialties->count()]->id,
                'avatar_url' => $avatars[18],
                'experience_years' => 17,
                'fee' => 700000,
            ],
            [
                'name' => 'Phó Giáo sư, Tiến sĩ, Bác sĩ Nguyễn Đức Thành',
                'email' => 'doctor20@mediconnect.local',
                'specialty_id' => $specialties[1 % $specialties->count()]->id,
                'avatar_url' => $avatars[19],
                'experience_years' => 23,
                'fee' => 800000,
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



            foreach ([0, 1, 2, 3, 4, 5, 6] as $dow) {
                DoctorWorkingHour::updateOrCreate(
                    [
                        'doctor_profile_id' => $profile->id,
                        'day_of_week' => $dow,
                    ],
                    [
                        'clinic_branch_id' => $branch->id,
                        'start_time' => '08:00:00',
                        'end_time' => '17:00:00',
                        'slot_minutes' => 30,
                    ]
                );
            }
        }

        $this->command->info("✅ Seed 20 doctors + working hours done.");
    }
}
