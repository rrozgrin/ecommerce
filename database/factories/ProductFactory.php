<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'brand_id' => Brand::factory(),
            'category_id' => Category::factory(),
            'name' => fake()->words(3, true),
            'price' => fake()->randomFloat(2, 10, 500),
            'discount' => 0,
            'amount' => fake()->numberBetween(1, 100),
            'image' => 'products/example.jpg',
        ];
    }
}
