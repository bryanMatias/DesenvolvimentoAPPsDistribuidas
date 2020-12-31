<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Hash;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if($user->blocked){
                return response()->json(
                    ['message' => 'Utilizador bloqueado.'],
                    401
                );
            }

            if($user->type === 'C'){
                $customer_data = Customer::where('id', $user->id)->firstOrFail();
                //return $customer_data;
                $user->address = $customer_data->address;
                $user->phone = $customer_data->phone;
                $user->nif = $customer_data->nif;
            }

            return $user;
        } else {
            return response()->json(
                ['message' => 'Autenticação invalida.'],
                401
            );
        }
    }

    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|mimes:jpg,jpeg,png|max:2048'
        ], [
            'photo.required' => 'Não foi recebida uma foto',
            'photo.mimes' => 'A foto tem que vir no format .jpg ou .png',
            'photo.max' => 'O tamanho não pode ser superior a 2048??',
        ]);

        $path = $request->photo->store('public/fotos');
        return basename($path);
    }

    public function signup(Request $request)
    { 
        $validatedData = $request->validate([
            'fullName' => ['required', 'regex:/^([a-zA-ZàèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇÅå]+\ )*([a-zA-ZàèìòùÀÈÌÒÙáéíóúýÁÉÍÓÚÝâêîôûÂÊÎÔÛãñõÃÑÕäëïöüÿÄËÏÖÜŸçÇÅå]+)$/i'],//mg???
            'email' => ['required', 'email', 'unique:users,email'],
            'address' => ['required'],
            'phone' => ['required', 'numeric'],
            'nif' => ['nullable', 'numeric', 'digits:9'],
            'password' => ['required', 'min:3', 'confirmed'],
            'photo_url' => ['nullable'],
        ], [
			'fullName.required' => 'O utilizador tem que ter um nome',
            'fullName.regex' => 'O formato do nome não é valido. Tem que conter apenas letras e palavras separadas por um único espaço',
			'email.required' => 'O utilizador tem que ter um e-mail',
			'email.unique' => 'Este e-mail já está a ser utilizado',
			'email.email' => 'Tem que introduzir um e-mail válido',
			'address.required' => 'O utilizador tem que inserir uma morada',
			'phone.required' => 'O utilizador tem que inserir o número de telefone',
			'phone.numeric' => 'Número de telefone inválido',
			'nif.numeric' => 'NIF inválido. Tem que ser numerico',
			'nif.digits' => 'NIF inválido. Tem que ter 9 digitos',
			'password.required' => 'Tem que introduzir uma password',
			'password.min' => 'Password tem que conter pelo menos 3 caracteres',
			'password.confirmation' => 'Password repetida não coincide',
        ]);
        $validatedData['password'] = Hash::make($validatedData['password']);

        /*TRATAR DE FAZER STORE A UM CLIENTE E UTILIZADOR NOVO*/
        $new_user = User::create(array(
            'name' => $validatedData['fullName'],
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
            'type' => 'C',
            'blocked' => '0',
            'photo_url' => $validatedData['photo_url'],
        ));

        $new_customer = Customer::create(array(
            'id' => $new_user->id,
            'address' => $validatedData['address'],
            'phone' => $validatedData['phone'],
            'nif' => $validatedData['nif'],
        ));

        return $new_customer;
        
    }

    public function logout()
    {
        Auth::guard('web')->logout(); //check if Auth::logout(); works
        return response()->json(['msg' => 'User session closed'], 200);
    }
}
