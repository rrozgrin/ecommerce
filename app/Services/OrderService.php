<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function create(int $userId, array $data): Order
    {
        $location = Location::whereKey($data['location_id'])
            ->where('user_id', $userId)
            ->firstOrFail();

        return DB::transaction(function () use ($data, $location, $userId) {
            $productIds = collect($data['order_items'])->pluck('product_id');
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $totalInCents = 0;

            foreach ($data['order_items'] as $orderItem) {
                $product = $products->get($orderItem['product_id']);

                if (! $product || $product->amount < $orderItem['quantity']) {
                    throw ValidationException::withMessages([
                        'order_items' => ['Estoque insuficiente para um dos produtos selecionados.'],
                    ]);
                }

                $totalInCents += $this->unitPriceInCents($product) * $orderItem['quantity'];
            }

            $order = Order::create([
                'user_id' => $userId,
                'location_id' => $location->id,
                'total_price' => $this->formatCents($totalInCents),
                'date_of_delivery' => $data['date_of_delivery'],
            ]);

            foreach ($data['order_items'] as $orderItem) {
                $product = $products->get($orderItem['product_id']);

                OrderItems::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $orderItem['quantity'],
                    'price' => $this->formatCents($this->unitPriceInCents($product)),
                ]);

                $product->decrement('amount', $orderItem['quantity']);
            }

            return $order->load('items.product', 'location');
        });
    }

    private function unitPriceInCents(Product $product): int
    {
        return max(0, (int) round(((float) $product->price - (float) ($product->discount ?? 0)) * 100));
    }

    private function formatCents(int $amountInCents): string
    {
        return number_format($amountInCents / 100, 2, '.', '');
    }
}
