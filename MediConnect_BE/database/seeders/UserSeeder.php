<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'admin')->first();
        $patientRole = Role::where('name', 'patient')->first();

        User::firstOrCreate([
            'email' => 'admin@mediconnect.local',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('123456'),
            'role_id' => $adminRole->id,
            'status' => 'active',
            'phone' => '0900000000',
        ]);

        User::firstOrCreate([
            'email' => 'patient@mediconnect.local',
        ], [
            'name' => 'Patient Test',
            'password' => Hash::make('123456'),
            'role_id' => $patientRole->id,
            'status' => 'active',
            'phone' => '0900000001',
        ]);
    }
}
