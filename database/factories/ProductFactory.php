<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get random category and unit
        $category = Category::inRandomOrder()->first() ?? Category::factory()->create();
        $unit = Unit::where('name', 'Pieces')->first() ?? Unit::factory()->create(['name' => 'Pieces']);

        // Generate realistic product names based on category
        $productNames = $this->getProductNamesByCategory($category->name);
        $productName = $this->faker->randomElement($productNames);

        // Add uniqueness to product name to reduce conflicts
        $uniqueProductName = $this->makeProductNameUnique($productName, $category->name);

        // Generate prices ensuring selling_price > cost_price
        $costPrice = $this->faker->randomFloat(2, 5, 100);
        $profitMargin = $this->faker->randomFloat(2, 0.2, 0.8); // 20% to 80% profit margin
        $sellingPrice = $costPrice * (1 + $profitMargin);

        // Generate stock levels
        $stock = $this->faker->numberBetween(0, 500);
        $alertStock = $this->faker->numberBetween(5, 50);

        return [
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'name' => $uniqueProductName,
            'slug' => $this->generateUniqueSlug($uniqueProductName),
            'sku' => $this->generateUniqueSKU($category->id),
            'cost_price' => $costPrice,
            'selling_price' => $sellingPrice,
            'stock' => $stock,
            'alert_stock' => $alertStock,
            'description' => $this->generateProductDescription($category->name, $uniqueProductName),
            'status' => true,
            'featured' => $this->faker->boolean(30), // 30% chance of being featured
            'not_for_sale' => $this->faker->boolean(10), // 10% chance of not being for sale
        ];
    }

    /**
     * Generate a unique SKU for a product.
     */
    private function generateUniqueSKU($categoryId): string
    {
        $prefix = 'PRD';
        $categoryIdPadded = str_pad($categoryId, 2, '0', STR_PAD_LEFT);
        $maxAttempts = 10;
        $attempts = 0;

        do {
            $unique = strtoupper(Str::random(5));
            $sku = "{$prefix}-{$categoryIdPadded}-{$unique}";
            $attempts++;

            // Check if SKU already exists
            $exists = \App\Models\Product::where('sku', $sku)->exists();
        } while ($exists && $attempts < $maxAttempts);

        if ($exists) {
            // Fallback: use timestamp if still collision after max attempts
            $unique = strtoupper(substr(md5(uniqid()), 0, 5));
            $sku = "{$prefix}-{$categoryIdPadded}-{$unique}";
        }

        return $sku;
    }

    /**
     * Generate a unique slug for a product.
     */
    private function generateUniqueSlug($productName): string
    {
        $baseSlug = Str::slug($productName);
        $maxAttempts = 10;
        $attempts = 0;

        do {
            $slug = $attempts > 0 ? "{$baseSlug}-{$attempts}" : $baseSlug;
            $attempts++;

            // Check if slug already exists
            $exists = \App\Models\Product::where('slug', $slug)->exists();
        } while ($exists && $attempts < $maxAttempts);

        if ($exists) {
            // Fallback: use random string if still collision after max attempts
            $slug = "{$baseSlug}-" . Str::random(5);
        }

        return $slug;
    }

    /**
     * Get product names based on category.
     */
    private function getProductNamesByCategory(string $category): array
    {
        return match($category) {
            'Electronics' => [
                'Wireless Bluetooth Headphones',
                'Smartphone 128GB',
                'Laptop 15.6" Display',
                'Tablet 10" Screen',
                'Smart Watch Series 5',
                'Wireless Mouse',
                'USB-C Hub Adapter',
                'Portable Power Bank',
                'Wireless Charging Pad',
                '4K Webcam',
                'Gaming Keyboard RGB',
                'External SSD 1TB',
                'Bluetooth Speaker',
                'Digital Camera 24MP',
                'Drone with 4K Camera',
                'Smart TV 55" 4K',
                'Gaming Console',
                'Wireless Earbuds',
                'Smart Home Hub',
                'Fitness Tracker',
                'Portable Projector',
                'Electric Toothbrush',
                'Smart Light Bulbs',
                'Wireless Charger Stand',
                'Action Camera 4K',
                'Smart Thermostat',
                'Streaming Device',
                'Electric Scooter',
            ],
            'Clothing & Apparel' => [
                'Cotton T-Shirt',
                'Denim Jeans',
                'Hoodie Sweatshirt',
                'Winter Jacket',
                'Running Shoes',
                'Leather Boots',
                'Summer Dress',
                'Business Shirt',
                'Yoga Pants',
                'Wool Sweater',
                'Baseball Cap',
                'Leather Belt',
                'Sunglasses UV Protection',
                'Backpack 25L',
                'Sports Shorts',
                'Polo Shirt',
                'Cargo Pants',
                'Windbreaker Jacket',
                'Swim Trunks',
                'Wool Coat',
                'Canvas Sneakers',
                'Dress Shoes',
                'Gloves Winter',
                'Scarf Cotton',
                'Beanie Hat',
                'Tank Top',
                'Track Pants',
                'Flip Flops',
            ],
            'Home & Garden' => [
                'Coffee Maker',
                'Air Purifier',
                'LED Desk Lamp',
                'Kitchen Knife Set',
                'Non-stick Frying Pan',
                'Blender 1000W',
                'Toaster Oven',
                'Vacuum Cleaner',
                'Iron Steam',
                'Wall Clock',
                'Throw Pillows Set',
                'Plant Pot Ceramic',
                'Garden Tools Set',
                'Watering Can',
                'Outdoor Chair',
                'Microwave Oven',
                'Dining Table Set',
                'Mattress Queen Size',
                'Shower Curtain',
                'Bath Towel Set',
                'Cookware Set',
                'Washing Machine',
                'Refrigerator Double Door',
                'Air Conditioner',
                'Rug Living Room',
                'Mirror Bathroom',
            ],
            'Sports & Outdoors' => [
                'Yoga Mat Premium',
                'Dumbbells Set 20kg',
                'Tennis Racket',
                'Basketball Official',
                'Camping Tent 4-Person',
                'Sleeping Bag',
                'Hiking Backpack',
                'Bicycle Helmet',
                'Resistance Bands Set',
                'Jump Rope Speed',
                'Golf Balls Set',
                'Fishing Rod',
                'Skateboard Complete',
                'Swimming Goggles',
                'Running Belt',
                'Mountain Bike',
                'Tennis Shoes',
                'Soccer Ball',
                'Football Jersey',
                'Boxing Gloves',
                'Treadmill Electric',
                'Exercise Bench',
                'Camping Lantern',
                'Hydration Backpack',
                'Climbing Rope',
                'Kayak Inflatable',
            ],
            'Books & Media' => [
                'Fiction Novel Bestseller',
                'Programming Guide',
                'Cookbook International',
                'Self-Help Book',
                'Children Story Book',
                'History Documentary',
                'Music Album Pop',
                'Audio Book Fiction',
                'Magazine Subscription',
                'Comic Book Series',
                'Educational DVD',
                'Language Learning Set',
                'Art Book Collection',
                'Travel Guide',
                'Science Journal',
                'Biography Memoir',
                'Mystery Thriller',
                'Romance Novel',
                'Science Fiction Book',
                'Poetry Collection',
                'Textbook College',
                'Photography Book',
                'History Book World',
                'Philosophy Essays',
                'Business Strategy Guide',
                'Psychology Today',
            ],
            'Toys & Games' => [
                'Board Game Strategy',
                'Puzzle 1000 Pieces',
                'Action Figure Hero',
                'Building Blocks Set',
                'Remote Control Car',
                'Video Game Console',
                'Card Game Deck',
                'Doll House Playset',
                'Train Set Electric',
                'Robot Toy Interactive',
                'Play-Doh Set',
                'Coloring Book Set',
                'Musical Toy Keyboard',
                'Stuffed Animal Bear',
                'Outdoor Swing Set',
                'LEGO Building Kit',
                'Video Game Racing',
                'Toy Car Collection',
                'Board Game Family',
                'Science Kit Chemistry',
                'Art Supplies Set',
                'Puppet Show Theater',
                'Toy Kitchen Set',
                'Educational Tablet',
                'Drum Set Kids',
                'Paint Set Watercolor',
                'Model Airplane Kit',
            ],
            default => [
                'Premium Product Item',
                'Quality Product',
                'Best Seller Product',
                'New Arrival Product',
                'Limited Edition Product',
            ],
        };
    }

    /**
     * Make product name unique by adding suffix or modifier.
     */
    private function makeProductNameUnique($productName, $category): string
    {
        $suffixes = [
            'Pro', 'Plus', 'Elite', 'Max', 'Ultra', 'Premium', 'Advanced', 
            'Deluxe', 'Professional', 'Standard', 'Classic', 'Modern', 
            'Digital', 'Smart', 'Wireless', 'Compact', 'Heavy Duty'
        ];

        $brands = [
            'TechPro', 'UltraTech', 'MasterCraft', 'EliteSeries', 'ProMax',
            'SmartLife', 'DigitalEdge', 'PowerTech', 'MegaStore', 'PremiumLine'
        ];

        $versions = [
            '2024 Edition', 'v2.0', 'Series X', 'Generation 3', 'Model Z',
            'Limited Edition', 'Special Edition', 'Anniversary Edition'
        ];

        // 40% chance to add a suffix
        if ($this->faker->boolean(40)) {
            return $productName . ' ' . $this->faker->randomElement($suffixes);
        }
        
        // 30% chance to add a brand name
        if ($this->faker->boolean(30)) {
            return $this->faker->randomElement($brands) . ' ' . $productName;
        }
        
        // 20% chance to add a version
        if ($this->faker->boolean(20)) {
            return $productName . ' ' . $this->faker->randomElement($versions);
        }
        
        // 10% chance to add a random color or size
        if ($this->faker->boolean(10)) {
            $colors = ['Red', 'Blue', 'Black', 'White', 'Green', 'Gray', 'Silver', 'Gold'];
            $sizes = ['Small', 'Medium', 'Large', 'X-Large'];
            
            if (in_array($category, ['Clothing & Apparel', 'Sports & Outdoors'])) {
                return $productName . ' (' . $this->faker->randomElement($colors) . ', ' . $this->faker->randomElement($sizes) . ')';
            } else {
                return $productName . ' (' . $this->faker->randomElement($colors) . ')';
            }
        }
        
        // Return original name if no modification
        return $productName;
    }

    /**
     * Generate realistic product description.
     */
    private function generateProductDescription(string $category, string $productName): string
    {
        $descriptions = [
            'High-quality product designed for maximum performance and durability.',
            'Premium materials ensure long-lasting use and customer satisfaction.',
            'Innovative design meets functionality in this exceptional product.',
            'Carefully crafted with attention to detail and user experience.',
            'Professional-grade quality suitable for both home and commercial use.',
            'Eco-friendly materials combined with modern manufacturing techniques.',
            'Versatile product that adapts to various needs and preferences.',
            'Cutting-edge technology integrated into user-friendly design.',
            'Stylish and practical solution for everyday requirements.',
            'Engineered to exceed expectations and deliver outstanding value.',
        ];

        return $this->faker->randomElement($descriptions) . ' ' . 
               $this->faker->sentence(10) . ' ' . 
               $this->faker->sentence(8);
    }
}
