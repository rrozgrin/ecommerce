<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    public function test_login_requires_email_and_password(): void
    {
        $this->postJson('/api/auth/login')
            ->assertUnprocessable()
            ->assertJsonStructure(['email', 'password']);
    }

    public function test_orders_require_authentication(): void
    {
        $this->getJson('/api/compras/index')
            ->assertUnauthorized();
    }

    public function test_addresses_require_authentication(): void
    {
        $this->postJson('/api/enderecos/store', [
            'street' => 'Rua Teste',
            'building' => '10',
            'area' => 'Centro',
        ])->assertUnauthorized();
    }
}
