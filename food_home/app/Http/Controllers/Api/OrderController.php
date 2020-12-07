<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;

use App\Http\Resources\OrderItem as OrderItemResource;

class OrderController extends Controller
{
    public function get()
    {
        return Order::all();
        //        return Order::with('orderItem')->all();
    }

    public function getOrderById(Order $order)
    {
        //$order['order_items'] = OrderItemResource::collection($order->orderItems);
        $order->orderItems;

        foreach ($order->orderItems as $orderItem) {
            $orderItem->product;
        }

        return $order;
    }

    public function getOrderItemsFromOrder(Order $order)
    {

        foreach ($order->orderItems as $orderItem) {
            $orderItem->product;
        }

        return $order->orderItems;
    }

    /////////////////////////////////////////////
    /* FUNÇÕES PARA ORDERS NO ESTADO READY 'R' */
    /////////////////////////////////////////////

    public function getOrdersReady()
    {
        $orders = Order::where('status', 'R')
            ->orderBy('current_status_at', 'desc')
            ->paginate(8);

        foreach ($orders as $order) {
            $order->customer;
        }
        return $orders;
    }

    public function putOrderTransit(Order $order)
    {
        //validar se a encomenda é de facto valida para se atualizar... tem que ser feita por um deliveryMan autenticado!

        $updateTime = now();
        $order->status = 'T';
        $order->current_status_at = $updateTime;
        $order->updated_at = $updateTime;
        //atualizar o id do deliveryMan!!
        $order->save();
    }

    ///////////////////////////////////////////////
    /* FUNÇÕES PARA ORDERS NO ESTADO TRANSIT 'T' */
    ///////////////////////////////////////////////

    public function getOrdersTransit()
    {
        $orders = Order::where('status', 'T')->get();

        foreach ($orders as $order) {
            $order->customer;
        }

        return $orders;
    }

    public function getDeliveryManCurrentOrder(User $user)
    {
        return Order::where('status', 'T')
            ->where('delivered_by', $user->id)
            ->firstOrFail();
    }
}
