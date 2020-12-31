<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name', 'type', 'description', 'price', 'photo_url',
    ];

    public function orderItems()
    {
        return $this->hasMany('App\Models\OrderItem', 'product_id', 'id');
    }

}
