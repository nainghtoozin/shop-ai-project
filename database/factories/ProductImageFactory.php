<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductImage>
 */
class ProductImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate fake image filename using Unsplash-style placeholder
        $imageSeeds = ['product', 'item', 'goods', 'merchandise', 'commodity'];
        $seed = $this->faker->randomElement($imageSeeds);
        $width = $this->faker->numberBetween(400, 800);
        $height = $this->faker->numberBetween(400, 800);
        $imageId = $this->faker->unique()->numberBetween(1, 1000);
        
        $filename = "product_{$imageId}.jpg";

        return [
            'product_id' => Product::factory(),
            'image' => $filename,
            'is_primary' => false,
            'sort_order' => 0,
        ];
    }

    /**
     * Indicate that the image is primary.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }

    /**
     * Create images for a specific product.
     */
    public function forProduct(Product $product): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => $product->id,
        ]);
    }
}
