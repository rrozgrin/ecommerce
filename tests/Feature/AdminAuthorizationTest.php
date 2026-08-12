<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_user_cannot_list_brands(): void
    {
        $this->getJson('/api/marcas', $this->headersFor(User::factory()->create()))
            ->assertForbidden();
    }

    public function test_admin_can_list_brands(): void
    {
        $this->getJson('/api/marcas', $this->headersFor(User::factory()->admin()->create()))
            ->assertOk();
    }

    private function headersFor(User $user): array
    {
        return ['Authorization' => 'Bearer '.auth('api')->login($user)];
    }
}
