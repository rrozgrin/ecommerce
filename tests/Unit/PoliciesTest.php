<?php

namespace Tests\Unit;

use App\Models\Location;
use App\Models\Order;
use App\Models\User;
use App\Policies\LocationPolicy;
use App\Policies\OrderPolicy;
use App\Policies\ProductPolicy;
use PHPUnit\Framework\TestCase;

class PoliciesTest extends TestCase
{
    public function test_only_admins_can_manage_products(): void
    {
        $policy = new ProductPolicy();

        $this->assertTrue($policy->create($this->user(1, true)));
        $this->assertFalse($policy->create($this->user(2, false)));
    }

    public function test_only_the_location_owner_can_change_it(): void
    {
        $policy = new LocationPolicy();
        $location = new Location(['user_id' => 1]);

        $this->assertTrue($policy->update($this->user(1), $location));
        $this->assertFalse($policy->delete($this->user(2), $location));
    }

    public function test_orders_are_visible_to_the_owner_or_an_admin(): void
    {
        $policy = new OrderPolicy();
        $order = new Order(['user_id' => 1]);

        $this->assertTrue($policy->view($this->user(1), $order));
        $this->assertTrue($policy->view($this->user(2, true), $order));
        $this->assertFalse($policy->view($this->user(2), $order));
    }

    private function user(int $id, bool $isAdmin = false): User
    {
        $user = new User();
        $user->forceFill([
            'id' => $id,
            'is_admin' => $isAdmin,
        ]);

        return $user;
    }
}
