<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_in_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'user@example.test',
            'password' => bcrypt('password'),
        ]);

        $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertOk()
            ->assertJsonStructure(['access_token', 'token_type', 'expires_in', 'user']);
    }
}
