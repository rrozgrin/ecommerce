<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\OrderItemResource;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Auth;


class OrderController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return response()->json('Usuário não autenticado', 401);
        }

        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product', 'location'])
            ->get();

        return ApiResponse::success(OrderResource::collection($orders));
    }

    public function show($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('items.product', 'location')
            ->firstOrFail();

        return ApiResponse::success(new OrderResource($order));
    }

    public function store(StoreOrderRequest $request, OrderService $orderService)
    {
        $order = $orderService->create(Auth::id(), $request->validated());

        return ApiResponse::created(new OrderResource($order), 'Compra adicionada com sucesso.');
    }

    public function getOrderItems(Order $order)
    {
        return ApiResponse::success(OrderItemResource::collection($order->items()->with('product')->get()));
    }

    public function getUserOrders($userId)
    {
        $orders = Order::with(['items.product', 'location'])->where('user_id', $userId)->get();

        return ApiResponse::success(OrderResource::collection($orders));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $order->update($request->validated());

        return ApiResponse::success(
            new OrderResource($order->load(['items.product', 'location'])),
            'Status alterado com sucesso.'
        );
    }
}
