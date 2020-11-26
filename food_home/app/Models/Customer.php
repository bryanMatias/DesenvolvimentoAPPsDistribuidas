<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Customer extends User
{
    use HasFactory;

    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'id');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'customer_id', 'id');
    }

}
