<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            SpecialtySeeder::class,
            ClinicSeeder::class,
            UserSeeder::class,
            DoctorSeeder::class,
            DoctorWorkingHourSeeder::class,
            DoctorSlotSeeder::class,
        ]);
    }
}
