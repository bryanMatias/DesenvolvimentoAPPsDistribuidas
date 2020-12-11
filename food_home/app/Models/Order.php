<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public function orderItems()
	{
		return $this->hasMany('App\Models\OrderItem', 'order_id', 'id');
	}

	public function customer()
	{
		return $this->belongsTo('App\Models\Customer', 'customer_id', 'id');
	}

	public function deliveryMan()
	{
		return $this->belongsTo('App\Models\DeliveryMan', 'delivered_by', 'id');
	}

}