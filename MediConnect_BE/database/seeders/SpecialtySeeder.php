<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Specialty;
use Illuminate\Support\Str;

class SpecialtySeeder extends Seeder
{
    public function run(): void
    {
        $specialties = [
            'Nội khoa',
            'Ngoại khoa',
            'Da liễu',
            'Tai mũi họng',
            'Nhi khoa',
            'Răng hàm mặt',
            'Tim mạch',
            'Thần kinh',
            'Sản phụ khoa',
            'Chấn thương chỉnh hình',
        ];

        foreach ($specialties as $name) {
            Specialty::firstOrCreate([
                'slug' => Str::slug($name),
            ], [
                'name' => $name,
                'description' => "Chuyên khoa {$name}",
            ]);
        }
    }
}
