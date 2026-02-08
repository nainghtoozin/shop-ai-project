<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        // MMK base charges (demo). Keep realistic and easy to test.
        $cities = [
            ['name' => 'Yangon', 'base_charge' => 3000, 'is_active' => true],
            ['name' => 'Mandalay', 'base_charge' => 5000, 'is_active' => true],
            ['name' => 'Nay Pyi Taw', 'base_charge' => 4500, 'is_active' => true],
            ['name' => 'Bago', 'base_charge' => 3500, 'is_active' => true],
            ['name' => 'Mawlamyine', 'base_charge' => 5500, 'is_active' => true],
            ['name' => 'Pathein', 'base_charge' => 4500, 'is_active' => true],
            ['name' => 'Taunggyi', 'base_charge' => 6000, 'is_active' => true],

            // Expanded realistic cities for testing
            ['name' => 'Pyay', 'base_charge' => 4000, 'is_active' => true],
            ['name' => 'Monywa', 'base_charge' => 5200, 'is_active' => true],
            ['name' => 'Hpa-An', 'base_charge' => 5200, 'is_active' => true],
            ['name' => 'Dawei', 'base_charge' => 6500, 'is_active' => true],
            ['name' => 'Lashio', 'base_charge' => 7000, 'is_active' => true],
            ['name' => 'Myitkyina', 'base_charge' => 8500, 'is_active' => true],
            ['name' => 'Sittwe', 'base_charge' => 9000, 'is_active' => true],
        ];

        foreach ($cities as $data) {
            City::query()->updateOrCreate(
                ['name' => $data['name']],
                ['base_charge' => $data['base_charge'], 'is_active' => $data['is_active']]
            );
        }
    }
}
