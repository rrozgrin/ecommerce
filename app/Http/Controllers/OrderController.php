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
        $this->authorize('viewAny', Order::class);

        $orders = Order::where('user_id', Auth::id())
            ->with(['items.product', 'location'])
            ->get();

        return ApiResponse::success(OrderResource::collection($orders));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load(['items.product', 'location']);

        return ApiResponse::success(new OrderResource($order));
    }

    public function store(StoreOrderRequest $request, OrderService $orderService)
    {
        $this->authorize('create', Order::class);

        $order = $orderService->create(Auth::id(), $request->validated());

        return ApiResponse::created(new OrderResource($order), 'Compra adicionada com sucesso.');
    }

    public function getOrderItems(Order $order)
    {
        $this->authorize('viewItems', $order);

        return ApiResponse::success(OrderItemResource::collection($order->items()->with('product')->get()));
    }

    public function getUserOrders($userId)
    {
        $this->authorize('viewUserOrders', Order::class);

        $orders = Order::with(['items.product', 'location'])->where('user_id', $userId)->get();

        return ApiResponse::success(OrderResource::collection($orders));
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $this->authorize('updateStatus', $order);

        $order->update($request->validated());

        return ApiResponse::success(
            new OrderResource($order->load(['items.product', 'location'])),
            'Status alterado com sucesso.'
        );
    }
}
