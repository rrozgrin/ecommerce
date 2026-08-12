<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Order $order): bool
    {
        return $user->is_admin || $user->id === $order->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function viewItems(User $user, Order $order): bool
    {
        return $user->is_admin;
    }

    public function viewUserOrders(User $user): bool
    {
        return $user->is_admin;
    }

    public function updateStatus(User $user, Order $order): bool
    {
        return $user->is_admin;
    }
}
