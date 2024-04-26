<?php

namespace App\Http\Controllers;

use App\Models\Produtc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProdutcController extends Controller
{
    public function index()
    {
        $products = Produtc::paginate(10);
        if ($products) {
            return response()->json($products, 200);
        } else return response()->json('Produtos não encontrados');
    }

    public function show($id)
    {
        $product = Produtc::find($id);
        if ($product) {
            return response()->json($product, 200);
        } else return response()->json('Produto não encontrados');
    }

    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
            'brand_id' => 'required|numeric',
            'discount' => 'numeric',
            'amount' => 'required|numeric',
            'image' => 'required',
        ]);

        $product = new Produtc();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->discount = $request->discount;
        $product->amount = $request->amount;
        if ($request->hasFile('image')) {
            $path = 'assets/uploads/product' . $product->image;
            if (File::exists($path)) {
                File::delete($path);
            }
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            try {
                $file->move('assets/uploads/product', $filename);
            } catch (FileException $e) {
                dd($e);
            }
            $product->image = $filename;
        }
        $product->save();

        return response()->json('Produto adiconado com sucesso', 201);
    }

    public function update_product($id, Request $request)
    {
        Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|numeric',
            'brand_id' => 'required|numeric',
            'discount' => 'required|numeric',
            'amount' => 'required|numeric',
            'image' => 'required',
        ]);

        $product = Produtc::find($id);
        if ($product) {
            $product->name = $request->name;
            $product->price = $request->price;
            $product->category_id = $request->category_id;
            $product->brand_id = $request->brand_id;
            $product->discount = $request->discount;
            $product->amount = $request->amount;
            if ($request->hasFile('image')) {
                $path = 'assets/uploads/product' . $product->image;
                if (File::exist($path)) {
                    File::delete($path);
                }
                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $filename = time() . '.' . $ext;
                try {
                    $file->move('assets/uploads/product', $filename);
                } catch (FileException $e) {
                    dd($e);
                }
            }
            $product->image = $filename;
            $product->update();

            return response()->json('Produto atualizado com sucesso', 201);
        } else return response()->json('Produto não encontrado');
    }

    public function delete_product($id)
    {
        $product = Produtc::find($id);
        if ($product) {
            $product->delete();
            return response()->json('Produto excluído com sucesso');
        } else return response()->json('Produto não encontrado');
    }
}
