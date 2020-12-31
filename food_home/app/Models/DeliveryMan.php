<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryMan extends User
{

    protected $table = 'users';

/*     public function order()
    {
        return $this->hasOne('App\Models\Order', 'delivered_by', 'id');
    } */
}
