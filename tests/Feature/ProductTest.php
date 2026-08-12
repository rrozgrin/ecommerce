<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_is_public(): void
    {
        Product::factory(2)->create();

        $this->getJson('/api/produtos')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_admin_can_create_a_product_with_an_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();
        $brand = Brand::factory()->create();
        $category = Category::factory()->create();

        $response = $this->postJson('/api/produtos', [
            'name' => 'Produto de teste',
            'price' => 99.90,
            'discount' => 10,
            'amount' => 5,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'image' => UploadedFile::fake()->image('product.jpg'),
        ], $this->headersFor($admin));

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Produto de teste');

        $imagePath = $response->json('data.image');
        Storage::disk('public')->assertExists($imagePath);
        $this->assertDatabaseHas('products', ['name' => 'Produto de teste']);
    }

    private function headersFor(User $user): array
    {
        return ['Authorization' => 'Bearer '.auth('api')->login($user)];
    }
}
