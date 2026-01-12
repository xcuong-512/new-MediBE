<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Clinic;
use App\Models\ClinicBranch;

class ClinicSeeder extends Seeder
{
    public function run(): void
    {
        $clinic = Clinic::firstOrCreate([
            'name' => 'MediConnect Clinic',
        ], [
            'hotline' => '1900 9999',
            'email' => 'support@mediconnect.local',
            'description' => 'Phòng khám mẫu để test hệ thống',
        ]);

        ClinicBranch::firstOrCreate([
            'clinic_id' => $clinic->id,
            'name' => 'Cơ sở Quận 1',
        ], [
            'address' => 'Quận 1, TP.HCM',
            'open_time' => '08:00:00',
            'close_time' => '17:00:00',
        ]);

        ClinicBranch::firstOrCreate([
            'clinic_id' => $clinic->id,
            'name' => 'Cơ sở Thủ Đức',
        ], [
            'address' => 'Thủ Đức, TP.HCM',
            'open_time' => '08:00:00',
            'close_time' => '17:00:00',
        ]);
    }
}
