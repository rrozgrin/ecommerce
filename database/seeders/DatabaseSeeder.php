<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@ecommerce.test'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password'),
                'is_admin' => true,
            ]
        );

        $brands = Brand::factory(3)->create();
        $categories = Category::factory(3)->create();

        Product::factory(12)->make()->each(function (Product $product) use ($brands, $categories) {
            $product->brand_id = $brands->random()->id;
            $product->category_id = $categories->random()->id;
            $product->save();
        });
    }
}
