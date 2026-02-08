<?php

namespace Database\Seeders;

use App\Models\DeliveryType;
use Illuminate\Database\Seeder;

class DeliveryTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Demo speed types (MMK fixed extra charge)
        $types = [
            [
                'name' => 'Normal Delivery',
                'charge_type' => 'fixed',
                'extra_charge' => 0,
                'description' => 'Standard delivery (2-3 days)',
                'is_active' => true,
            ],
            [
                'name' => 'Express Delivery',
                'charge_type' => 'fixed',
                'extra_charge' => 3000,
                'description' => 'Fast delivery (same day / next day)',
                'is_active' => true,
            ],
        ];

        foreach ($types as $data) {
            DeliveryType::query()->updateOrCreate(
                ['name' => $data['name']],
                [
                    'charge_type' => $data['charge_type'],
                    'extra_charge' => $data['extra_charge'],
                    'description' => $data['description'],
                    'is_active' => $data['is_active'],
                ]
            );
        }
    }
}
