<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles & permissions
        $this->call([
            PermissionSeeder::class,
            SuperAdminSeeder::class,
        ]);

        // Create test user
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Run seeders in correct order
        // $this->call([
        //     UnitSeeder::class,
        //     CategorySeeder::class,
        // ]);

        // // Create 30 products with images
        // Product::factory(30)->create()->each(function ($product) {
        //     // Create 1-3 images for each product
        //     $imageCount = fake()->numberBetween(1, 3);

        //     ProductImage::factory($imageCount)
        //         ->forProduct($product)
        //         ->create()
        //         ->each(function ($image, $index) use ($product) {
        //             // Set first image as primary
        //             if ($index === 0) {
        //                 $image->is_primary = true;
        //                 $image->save();
        //             }

        //             // Set sort order
        //             $image->sort_order = $index + 1;
        //             $image->save();
        //         });
        // });
    }
}
