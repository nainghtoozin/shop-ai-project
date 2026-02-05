<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics',
                'code' => 'ELEC',
                'description' => 'Electronic devices, gadgets, and accessories including smartphones, laptops, tablets, and consumer electronics.',
            ],
            [
                'name' => 'Clothing & Apparel',
                'slug' => 'clothing-apparel',
                'code' => 'CLTH',
                'description' => 'Fashion items including shirts, pants, dresses, shoes, and accessories for men, women, and children.',
            ],
            [
                'name' => 'Home & Garden',
                'slug' => 'home-garden',
                'code' => 'HOME',
                'description' => 'Home improvement items, furniture, garden supplies, kitchen appliances, and home decor products.',
            ],
            [
                'name' => 'Sports & Outdoors',
                'slug' => 'sports-outdoors',
                'code' => 'SPRT',
                'description' => 'Athletic equipment, outdoor gear, fitness accessories, sportswear, and recreational items.',
            ],
            [
                'name' => 'Books & Media',
                'slug' => 'books-media',
                'code' => 'BOOK',
                'description' => 'Physical books, e-books, magazines, music CDs, DVDs, and educational media content.',
            ],
            [
                'name' => 'Toys & Games',
                'slug' => 'toys-games',
                'code' => 'TOYS',
                'description' => 'Children\'s toys, board games, video games, puzzles, and educational play items.',
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'code' => $category['code'],
                'description' => $category['description'],
                'status' => true,
            ]);
        }
    }
}
