<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;

class OrderItemController extends Controller
{
    public function get()
    {
        return OrderItem::all();
    }

    public function getOrderItemById(OrderItem $orderItem)
    {
        return $orderItem;
    }
}
