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
            'Internal Medicine',      
            'General Surgery',        
            'Dermatology',        
            'Otolaryngology',           
            'Pediatrics',              
            'Odonto-Stomatology',      
            'Cardiology',             
            'Neurology',           
            'Obstetrics and Gynecology',
            'Trauma and Orthopedics', 
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
