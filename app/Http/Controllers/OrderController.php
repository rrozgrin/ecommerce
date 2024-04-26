<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Produtc;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


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
                $product = Produtc::where('id', $order_items->product_id)->pluck('name');
                $order_items->product_name = $product['0'];
            }
        }
        return response()->json($orders, 200);
    }

    public function show($id)
    {
        $order = Order::fing($id);
        return response()->json($order, 200);
    }

    public function store(Request $request)
    {
        try {
            $location = Location::where('user_id', Auth::id())->first();
            // dd($location);
            $request->validate([
                'order_items' => 'required',
                'total_price' => 'required',
                'quantity' => 'required',
                'date_of_delivery' => 'required',
            ]);

            $order = new Order();
            $order->user_id = Auth::id();
            $order->location_id = $location->id;
            $order->total_price = $request->total_price;
            $order->date_of_delivery = $request->date_of_delivery;
            $order->save();

            foreach ($request->order_items as $order_items) {
                $items = new OrderItems();
                $items->order_id = $order->id;
                $items->price = $order_items['price'];
                $items->product_id = $order_items['product_id'];
                $items->quantity = $order_items['quantity'];
                $items->save();
                $product = Produtc::where('id', $order_items['product_id'])->first();
                $product->amount -= $order_items['quantity'];
                $product->save();
            }
            return response()->json('Compra adicionada com sucesso', 201);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function get_order_items($id)
    {
        $order_items = OrderItems::where('order_id', $id)->get();
        if ($order_items) {
            foreach ($order_items as $order_item) {
                $product = Produtc::where('id', $order_item->product_id)->pluck('name');
                $order_item->product_name = $product['0'];
            }
            return response()->json($order_items);
        } else return response()->json('Itens não localizados');
    }

    public function get_user_orders($id)
    {
        $orders = Order::with('items')->where('user_id', $id)->get();

        if ($orders) {
            foreach ($orders as $order) {
                foreach ($order->items as $order_items){
                    $product = Produtc::where('id', $order_items->product_id)->pluck('name');
                    $order_items->product_name = $product['0'];
                }

            }
            return response()->json($orders);
        } else return response()->json('Nenhuma compra encontrada para esse usuário');
    }

    public function change_order_status($id, Request $request)
    {
        $order = Order::find($id);
        if ($order) {
            $order->update(['status' => $request->status]);
            return response()->json('Status alterado com sucesso');
        } else return response()->json('Compra não foi encontrada');
    }
}
