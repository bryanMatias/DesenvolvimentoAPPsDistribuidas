<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Resources\Customer as CustomerResource;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;

class UserController extends Controller
{

    public function GetAuthUser(Request $request)
    {
        return $request->user();
    }

    public function GetAllUsers()
    {
        return User::all();
    }

    public function GetUserById(User $user)
    {
        return $user;
    }

    public function GetAllCustomers()
    {
        return CustomerResource::collection(Customer::all());
    }

    public function GetCustomerById(Customer $customer)
    {
        $user = $customer->user;
        $customer['name'] = $user->name;
        $customer['address'] = $user->address;
        $customer['email'] = $user->email;
        $customer['blocked'] = $user->blocked;
        $customer['photo_url'] = $user->photo_url;
        return $customer;
    }

    public function GetAllDeliveryMans()
    {
        return User::where('type', 'ED')->get();
    }
}
