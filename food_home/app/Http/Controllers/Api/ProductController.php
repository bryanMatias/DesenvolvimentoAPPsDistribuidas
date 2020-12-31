<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductController extends Controller
{
    public function get()
    {
        $products = Product::all();

        return $products;
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'unique:products,name'],
            'type' => ['required', 'regex:(hot dish|cold dish|drink|dessert)'],
            'description' => ['required'],
            'photo_url' => ['required'],
            'price' => ['required', 'max:10', 'regex:/^-?[0-9]+(\.[0-9]{1,2})?$/'],
        ], [
			'name.required' => 'O produto tem que ter um nome',
			'name.unique' => 'Já existe um produto com o mesmo nome',
			'type.required' => 'É obrigatório atribuir um tipo',
			'type.regex' => 'O tipo escolhido não existe',
			'description.required' => 'Tem que atribuir uma descrição ao produto',
			'photo_url.required' => 'Tem que atribuir uma foto ao produto',
			'price.required' => 'Tem que atribuir um preço ao produto',
			'price.max' => 'Número de digitos para o preço inválido',
			'price.regex' => 'Formato inválido para o preço',
        ]);

        $new_product = Product::create(array(
            'name' => $validatedData['name'],
            'type' => $validatedData['type'],
            'description' => $validatedData['description'],
            'photo_url' => $validatedData['photo_url'],
            'price' => $validatedData['price'],
        ));

        return $new_product;
    }

    public function destroy(Product $product)
    {
        Storage::delete("/public/products/".$product->photo_url);

        $product->delete();

        return $product;
    }

    public function update(Request $request, Product $product)
    {
        $validatedData = $request->validate([
            'name' => ['required', 'unique:products,name,'.$product->id],
            'type' => ['required', 'regex:(hot dish|cold dish|drink|dessert)'],
            'description' => ['required'],
            'photo_url' => ['nullable'],
            'price' => ['required', 'max:10', 'regex:/^-?[0-9]+(\.[0-9]{1,2})?$/'],
        ], [
			'name.required' => 'O produto tem que ter um nome',
			'name.unique' => 'Já existe um produto com o mesmo nome',
			'type.required' => 'É obrigatório atribuir um tipo',
			'type.regex' => 'O tipo escolhido não existe',
			'description.required' => 'Tem que atribuir uma descrição ao produto',
			'photo_url.required' => 'Tem que atribuir uma foto ao produto',
			'price.required' => 'Tem que atribuir um preço ao produto',
			'price.max' => 'Número de digitos para o preço inválido',
			'price.regex' => 'Formato inválido para o preço',
        ]);

        if($request->has('photo_url') && !is_null($request->photo_url)){
            Storage::delete("/public/products/".$product->photo_url);
            $product->photo_url = $validatedData['photo_url'];
        }

        $product->name = $validatedData['name'];
        $product->type = $validatedData['type'];
        $product->description = $validatedData['description'];
        $product->price = $validatedData['price'];

        $product->save();

        return $product;
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

        $path = $request->photo->store('public/products');
        return basename($path);
    }
}
