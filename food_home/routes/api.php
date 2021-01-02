<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;

use App\Http\Controllers\Api\AuthController;
use Illuminate\Contracts\Auth\UserProvider;

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

Route::post('login', [AuthController::class, 'login']);
Route::post('signup', [AuthController::class, 'signup']);
Route::post('upload-photo', [AuthController::class, 'uploadPhoto']);
Route::post('upload-photo-product', [ProductController::class, 'uploadPhoto']);

//Rotas protegidas para apenas utilizadores autenticados e não bloqueados
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::middleware(['non-blocked'])->group(function () {
        Route::get('/user/auth', [UserController::class, 'GetAuthUser']);
        Route::put('/user/{user}/update', [UserController::class, 'update']);
    
        //Rotas para DeliveryMans...
        Route::middleware(['isDeliveryMan'])->group(function () {
            Route::get('/deliverymans/{user}/order', [OrderController::class, 'getDeliveryManCurrentOrder']);
            Route::put('/orders/{order}/deliver', [OrderController::class, 'putOrderTransitToDelivered']);
            Route::put('/orders-ready/next', [OrderController::class, 'assignNextOrderReady']);
        });
    
        //Rotas para Managers...

    });

});

//Rotas para todos os utilizadores
Route::get('/users', [UserController::class, 'GetAllUsers']);
Route::get('/users/{user}', [UserController::class, 'GetUserById']);
Route::delete('/users/{user}', [UserController::class, 'destroy']);
Route::put('/users/{user}/flip-block', [UserController::class, 'flipBlock']);

//Rotas para os clientes
Route::get('/customers', [UserController::class, 'GetAllCustomers']);
Route::get('/customers/{customer}', [UserController::class, 'GetCustomerById']);

//Rotas para os empregados
Route::get('/employees', [UserController::class, 'GetAllEmployees']);

//Rotas para os cozinheiros
Route::get('/cookers', [UserController::class, 'GetAllCookers']);

//Rotas para o homens de entrega
Route::get('/deliverymans', [UserController::class, 'GetAllDeliveryMans']);

//Rotas para o manager
Route::get('/managers', [UserController::class, 'GetAllManagers']);

//Rotas para os produtos
Route::get('/products', [ProductController::class, 'get']);
Route::post('/products/create', [ProductController::class, 'create']);
Route::delete('/products/{product}', [ProductController::class, 'destroy']);
Route::put('/products/{product}', [ProductController::class, 'update']);

//Rotas para as encomendas em geral
Route::get('/orders', [OrderController::class, 'get']);
Route::get('/orders/{order}', [OrderController::class, 'getOrderById']);
Route::get('/orders/{order}/order-items', [OrderController::class, 'getOrderItemsFromOrder']);
//Route::get('/orders/{order}/order-items/{}', [OrderController::class, 'getOrderItemsFromOrder']);
Route::get('/active-orders', [OrderController::class, 'getActiveOrders']);
Route::put('/orders/{order}/cancel', [OrderController::class, 'cancelOrder']);

//Rotas para as encomendas em espera para preparar
Route::get('/orders-holding', [OrderController::class, 'getOrdersHolding']);
Route::get('/cooks/{user}/order', [UserController::class, 'getCookOrder']);

//Rotas para as encomendas prontas para entrega
Route::get('/orders-ready', [OrderController::class, 'getOrdersReady']);
Route::put('/orders-ready/{order}', [OrderController::class, 'putOrderReadyToTransit']);

//Rotas para as encomendas a serem entregues
Route::get('/orders-transit', [OrderController::class, 'getOrdersTransit']);

//Rotas para os items de cada encomenda
Route::get('/order-items', [OrderItemController::class, 'get']);
Route::get('/order-items/{order_item}', [OrderItemController::class, 'getOrderItemById']);
