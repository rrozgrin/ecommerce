<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'street' => fake()->streetName(),
            'building' => fake()->buildingNumber(),
            'area' => fake()->citySuffix(),
        ];
    }
}
