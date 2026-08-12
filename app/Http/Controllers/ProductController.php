<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(10);
        if ($products) {
            return response()->json($products, 200);
        } else return response()->json('Produtos não encontrados');
    }

    public function show(Product $product)
    {
        return response()->json($product, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
            'brand_id' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'amount' => 'required|numeric',
            'image' => 'required|image',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->discount = $request->discount;
        $product->amount = $request->amount;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('public/assets/uploads/product', $filename);
            $product->image = $filename;
        }
        $product->save();

        return response()->json('Produto adiconado com sucesso', 201);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
            'brand_id' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'amount' => 'required|numeric',
            'image' => 'nullable|image',
        ]);

        $product->name = $request->name;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->discount = $request->discount;
        $product->amount = $request->amount;
        if ($request->hasFile('image')) {
            $path = 'public/assets/uploads/product/' . $product->image;
            if (File::exists($path)) {
                File::delete($path);
            }
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('public/assets/uploads/product', $filename);
            $product->image = $filename;
        }
        $product->update();

        return response()->json('Produto atualizado com sucesso');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json('Produto excluído com sucesso');
    }
}
