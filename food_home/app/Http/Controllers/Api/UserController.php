<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Resources\Customer as CustomerResource;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Customer;
//use App\Models\DeliveryMan;

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

    public function GetAllEmployees()
    {
        return User::where('type', '!=', 'C')->get();
    }

    public function GetAllCookers()
    {
        return User::where('type', 'EC')->get();
    }

    public function GetAllDeliveryMans()
    {
        $delivery_mans = User::where('type', 'ED')->get();

        foreach ($delivery_mans as $delivery_man) {
            $delivery_man->order = Order::where('delivered_by', $delivery_man->id)->whereNull('closed_at')->first();
        }

        return $delivery_mans;
    }

    public function GetAllManagers()
    {
        return User::where('type', 'EM')->get();
    }

    public function update(Request $request, User $user)
    {
        if($user->type === 'C'){
            $validatedData = $request->validate([
                'name' => ['required', 'regex:/^([a-zA-ZàèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇÅå]+\ )*([a-zA-ZàèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇÅå]+)$/i'],
                'photo_url' => ['nullable'],//'nullable|image|max:8192', //8MB max
                'email' => ['required', 'email', 'unique:users,email,'.$user->id],
                'address' => ['required'],
                'phone' => ['required', 'numeric'],
                'nif' => ['nullable', 'numeric', 'digits:9'],
                'oldPassword' => ['nullable'],
                'password' => ['nullable', 'min:3', 'confirmed'],
            ], [
                'name.required' => 'O utilizador tem que ter um nome',
                'name.regex' => 'O formato do nome não é valido. Tem que conter apenas letras e palavras separadas por um único espaço',
                'email.required' => 'O utilizador tem que ter um e-mail',
                'email.unique' => 'Este e-mail já está a ser utilizado',
                'email.email' => 'Tem que introduzir um e-mail válido',
                'address.required' => 'O utilizador tem que inserir uma morada',
                'phone.required' => 'O utilizador tem que inserir o número de telefone',
                'phone.numeric' => 'Número de telefone inválido',
                'nif.numeric' => 'NIF inválido. Tem que ser numérico',
                'nif.digits' => 'NIF inválido. Tem que ter 9 digitos',
                'password.min' => 'Tem que introduzir uma password com um minimo de 3 characteres',
                'password.confirmed' => 'A repetição da nova password não coincide',
            ]);
        } else {
            $validatedData = $request->validate([
                'name' => ['required', 'regex:/^([a-zA-ZàèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇÅå]+\ )*([a-zA-ZàèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇÅå]+)$/i'],
                'photo_url' => ['nullable'],//'nullable|image|max:8192', //8MB max
                'email' => ['required', 'email', 'unique:users,email,'.$user->id],
                'oldPassword' => ['nullable'],
                'password' => ['nullable', 'min:3', 'confirmed'],
            ], [
                'name.required' => 'O utilizador tem que ter um nome',
                'name.regex' => 'O formato do nome não é valido. Tem que conter apenas letras e palavras separadas por um único espaço',
                'email.required' => 'O utilizador tem que ter um e-mail',
                'email.unique' => 'Este e-mail já está a ser utilizado',
                'email.email' => 'Tem que introduzir um e-mail válido',
                'password.min' => 'Tem que introduzir uma password com um minimo de 3 characteres',
                'password.confirmed' => 'A repetição da nova password não coincide',
            ]);
        }
        
        if($validatedData['password']){
            if(!Hash::check($validatedData['oldPassword'], $user->password)){
                throw ValidationException::withMessages(['password' => 'A password do utilizador não é valida']);
            }
            $validatedData['password'] = Hash::make($validatedData['password']);
        } else {
            $validatedData['password'] = $user->password;
        }
        
        if($request->has('photo_url')){
            
            if(!is_null($user->photo_url)){
                Storage::delete("/public/fotos/".$user->photo_url);
            }
            
            $user->photo_url = $validatedData['photo_url'];
            
        }
        
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->password = $validatedData['password'];
        $user->save();

        if($user->type === 'C'){
            $customer = Customer::where('id', $user->id)->firstOrFail();
            $customer->address = $validatedData['address'];
            $customer->phone = $validatedData['phone'];
            $customer->nif = $validatedData['nif'];
            $customer->save();

            $user->address = $customer->address;
            $user->phone = $customer->phone;
            $user->nif = $customer->nif;
        }

        return $user;    

    }
}