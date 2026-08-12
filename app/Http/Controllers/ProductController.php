<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    public function index()
    {
        return ApiResponse::success(ProductResource::collection(Product::with(['category', 'brand'])->paginate(10)));
    }

    public function show(Product $product)
    {
        return ApiResponse::success(new ProductResource($product->load(['category', 'brand'])));
    }

    public function store(StoreProductRequest $request)
    {
        $product = new Product($request->safe()->except('image'));
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $ext = $file->getClientOriginalExtension();
            $filename = time() . '.' . $ext;
            $file->move('public/assets/uploads/product', $filename);
            $product->image = $filename;
        }
        $product->save();

        return ApiResponse::created(
            new ProductResource($product->load(['category', 'brand'])),
            'Produto adicionado com sucesso.'
        );
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product->fill($request->safe()->except('image'));
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

        return ApiResponse::success(
            new ProductResource($product->load(['category', 'brand'])),
            'Produto atualizado com sucesso.'
        );
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return ApiResponse::noContent();
    }
}
