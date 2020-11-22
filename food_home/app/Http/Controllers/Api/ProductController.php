<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function get()
    {
        $products = Product::all();

        return $products;
    }
}
