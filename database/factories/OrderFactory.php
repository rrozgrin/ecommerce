<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'location_id' => Location::factory(),
            'status' => 'Pendente',
            'total_price' => fake()->randomFloat(2, 10, 500),
            'date_of_delivery' => fake()->date(),
        ];
    }
}
