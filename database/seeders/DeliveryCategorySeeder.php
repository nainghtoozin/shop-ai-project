<?php

namespace Database\Seeders;

use App\Models\DeliveryCategory;
use Illuminate\Database\Seeder;

class DeliveryCategorySeeder extends Seeder
{
    public function run(): void
    {
        // MMK extra charges per item (demo)
        $categories = [
            ['name' => 'normal', 'extra_charge' => 0],
            ['name' => 'fragile', 'extra_charge' => 2000],
            ['name' => 'heavy', 'extra_charge' => 3000],
            ['name' => 'oversized', 'extra_charge' => 4000],
        ];

        foreach ($categories as $data) {
            DeliveryCategory::query()->updateOrCreate(
                ['name' => $data['name']],
                ['extra_charge' => $data['extra_charge']]
            );
        }
    }
}
