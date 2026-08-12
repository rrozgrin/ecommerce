<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemsFactory extends Factory
{
    protected $model = OrderItems::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'quantity' => fake()->numberBetween(1, 5),
            'price' => fake()->randomFloat(2, 10, 500),
        ];
    }
}
