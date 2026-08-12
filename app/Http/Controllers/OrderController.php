<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class OrderController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return response()->json('Usuário não autenticado', 401);
        }

        $orders = Order::where('user_id', Auth::id())->with('user')->get();

        if ($orders->isEmpty()) {
            return response()->json('Nenhuma compra encontrada para esse usuário', 404);
        }
        foreach ($orders as $order) {
            foreach ($order->items as $order_items) {
                $product = Product::where('id', $order_items->product_id)->pluck('name');
                $order_items->product_name = $product['0'];
            }
        }
        return response()->json($orders, 200);
    }

    public function show($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('items.product', 'location')
            ->firstOrFail();

        return response()->json($order, 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'location_id' => 'required|integer|exists:locations,id',
            'date_of_delivery' => 'required|date',
            'order_items' => 'required|array|min:1',
            'order_items.*.product_id' => 'required|integer|distinct|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
        ]);

        $location = Location::where('id', $validated['location_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $order = DB::transaction(function () use ($validated, $location) {
            $productIds = collect($validated['order_items'])->pluck('product_id');
            $products = Product::whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $totalPrice = 0;

            foreach ($validated['order_items'] as $orderItem) {
                $product = $products->get($orderItem['product_id']);

                if (! $product || $product->amount < $orderItem['quantity']) {
                    throw ValidationException::withMessages([
                        'order_items' => ['Estoque insuficiente para um dos produtos selecionados.'],
                    ]);
                }

                $unitPrice = max(0, (float) $product->price - (float) ($product->discount ?? 0));
                $totalPrice += $unitPrice * $orderItem['quantity'];
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'location_id' => $location->id,
                'total_price' => $totalPrice,
                'date_of_delivery' => $validated['date_of_delivery'],
            ]);

            foreach ($validated['order_items'] as $orderItem) {
                $product = $products->get($orderItem['product_id']);
                $unitPrice = max(0, (float) $product->price - (float) ($product->discount ?? 0));

                OrderItems::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $orderItem['quantity'],
                    'price' => $unitPrice,
                ]);

                $product->decrement('amount', $orderItem['quantity']);
            }

            return $order->load('items.product');
        });

        return response()->json($order, 201);
    }

    public function getOrderItems(Order $order)
    {
        $order_items = OrderItems::where('order_id', $order->id)->get();
        if ($order_items) {
            foreach ($order_items as $order_item) {
                $product = Product::where('id', $order_item->product_id)->pluck('name');
                $order_item->product_name = $product['0'];
            }
            return response()->json($order_items);
        } else return response()->json('Itens não localizados');
    }

    public function getUserOrders($userId)
    {
        $orders = Order::with('items')->where('user_id', $userId)->get();

        if ($orders) {
            foreach ($orders as $order) {
                foreach ($order->items as $order_items){
                    $product = Product::where('id', $order_items->product_id)->pluck('name');
                    $order_items->product_name = $product['0'];
                }

            }
            return response()->json($orders);
        } else return response()->json('Nenhuma compra encontrada para esse usuário');
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:Pendente,Saiu para entrega,Cancelado,Entregue',
        ]);

        $order->update($validated);

        return response()->json('Status alterado com sucesso');
    }
}
