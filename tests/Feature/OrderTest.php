<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_total_is_calculated_on_the_server_and_stock_is_reduced(): void
    {
        $user = User::factory()->create();
        $location = Location::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create([
            'price' => 10,
            'discount' => 2,
            'amount' => 5,
        ]);

        $this->postJson('/api/compras', [
            'location_id' => $location->id,
            'date_of_delivery' => now()->addDay()->toDateString(),
            'total_price' => 99999,
            'order_items' => [[
                'product_id' => $product->id,
                'quantity' => 2,
                'price' => 99999,
            ]],
        ], $this->headersFor($user))
            ->assertCreated()
            ->assertJsonPath('data.total_price', '16.00');

        $this->assertDatabaseHas('orders', ['total_price' => '16.00']);
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => '8.00',
        ]);
        $this->assertSame(3, $product->fresh()->amount);
    }

    public function test_order_is_rejected_when_stock_is_insufficient(): void
    {
        $user = User::factory()->create();
        $location = Location::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create(['amount' => 1]);

        $this->postJson('/api/compras', [
            'location_id' => $location->id,
            'date_of_delivery' => now()->addDay()->toDateString(),
            'order_items' => [[
                'product_id' => $product->id,
                'quantity' => 2,
            ]],
        ], $this->headersFor($user))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('order_items');

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(1, $product->fresh()->amount);
    }

    private function headersFor(User $user): array
    {
        return ['Authorization' => 'Bearer '.auth('api')->login($user)];
    }
}
