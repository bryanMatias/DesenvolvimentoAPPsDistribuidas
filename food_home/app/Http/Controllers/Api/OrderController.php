<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Carbon\Carbon;

use App\Http\Resources\OrderItem as OrderItemResource;
use App\Models\DeliveryMan;

class OrderController extends Controller
{

    /////////////////////////////////////////////
    /*        FUNÇÕES PARA ORDERS GERAIS       */
    /////////////////////////////////////////////

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
            $orderItem->product = Product::withTrashed()->where('id', $orderItem->product_id)->first();
        }

        return $order->orderItems;
    }

    public function getActiveOrders(Request $request)
    {
        $orders = Order::whereIn('status', ['H', 'P', 'R', 'T'])
            ->orderBy('created_at')
            ->get();

        foreach($orders as $order)
        {
            switch($order->status){
                case 'P':
                    $order->employee = User::where('id', $order->prepared_by)->first();
                    break;
                case 'T':
                    $order->employee = User::where('id', $order->delivered_by)->first();
                    break;
                default:
                    $order->employee = null;
            }
        }

        return $orders;
    }

    /////////////////////////////////////////////
    /* FUNÇÕES PARA ORDERS NO ESTADO READY 'R' */
    /////////////////////////////////////////////

    public function getOrdersReady()
    {
        $orders = Order::where('status', 'R')
                    ->orderBy('current_status_at', 'DESC')
                    ->paginate(8);

        foreach ($orders as $order) {
            $order->customer;
        }
        return $orders;
    }

    public function assignNextOrderReady(Request $request)
    {
        $order = Order::where('status', 'R')
        ->orderBy('current_status_at','DESC')
        ->first();

        $updateTime = now();
        $order->status = 'T';
        $order->current_status_at = $updateTime;
        $order->updated_at = $updateTime;
        $order->delivered_by = $request->deliveryman_id; //GARANTIR AUTENTICIDADE DESTE ID. Eu podia mandar ID de outro DelviveryMan, como sei que este ID é válido?
        $order->save();
        
        return $order;
    }

    public function putOrderReadyToTransit(Order $order)
    {
        //validar se a encomenda é de facto valida para se atualizar... tem que ser feita por um deliveryMan autenticado!

        $updateTime = now();
        $order->status = 'T';
        $order->current_status_at = $updateTime;
        $order->updated_at = $updateTime;
        //falta total_time
        //atualizar o delivered_by do deliveryMan!!
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

    public function putOrderTransitToDelivered(Request $request, Order $order)
    {
        //validar se a encomenda é de facto valida para se atualizar... tem que ser feita por um deliveryMan autenticado!

        $updateTime = now();

        $startDel = Carbon::parse($request->deliverStart);
        $deliverDuration = $updateTime->diffInSeconds($startDel);

        $orderCreatedAt = Carbon::parse($order->created_at);
        $totalDuration = $updateTime->diffInSeconds($orderCreatedAt);
        
        $order->status = 'D';
        $order->current_status_at = $updateTime;
        $order->updated_at = $updateTime;
        $order->delivery_time = $deliverDuration;
        $order->total_time += $totalDuration;
        $order->closed_at = $updateTime;
        $order->save();

        return response()->json(
            ['message' => 'Entrega bem sucedida.'],
            200
        );

    }
}