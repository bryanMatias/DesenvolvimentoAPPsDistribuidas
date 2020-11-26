<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Rotas para todos os utilizadores
Route::get('/users', [UserController::class, 'GetAllUsers']);
Route::get('/users/{user}', [UserController::class, 'GetUserById']);

//Rotas para os clientes
Route::get('/customers', [UserController::class, 'GetAllCustomers']);
Route::get('/customers/{customer}', [UserController::class, 'GetCustomerById']);

//Rotas para o homens de entrega
Route::get('/delieverymans', [UserController::class, 'GetAllDelieveryMans']);

//Rotas para os produtos
Route::get('/products', [ProductController::class, 'get']);

//Rotas para as encomendas em geral
Route::get('/orders', [OrderController::class, 'get']);
Route::get('/orders/{order}', [OrderController::class, 'getOrderById']);
Route::get('/orders/{order}/order-items', [OrderController::class, 'getOrderItemsFromOrder']);
//Route::get('/orders/{order}/order-items/{}', [OrderController::class, 'getOrderItemsFromOrder']);

//Rotas para as encomendas prontas para entrega
Route::get('/orders-ready', [OrderController::class, 'getOrdersReady']);
Route::put('/orders-ready/{order}', [OrderController::class, 'putOrderTransit']);

//Rotas para os items de cada encomenda
Route::get('/order-items', [OrderItemController::class, 'get']);
Route::get('/order-items/{order_item}', [OrderItemController::class, 'getOrderItemById']);
