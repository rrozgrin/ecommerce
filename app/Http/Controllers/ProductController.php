<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Storage;

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
        $this->authorize('create', Product::class);

        $product = new Product($request->safe()->except('image'));
        if ($request->hasFile('image')) {
            $product->image = $request->file('image')->store('products', 'public');
        }
        $product->save();

        return ApiResponse::created(
            new ProductResource($product->load(['category', 'brand'])),
            'Produto adicionado com sucesso.'
        );
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        $product->fill($request->safe()->except('image'));
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->store('products', 'public');
        }
        $product->update();

        return ApiResponse::success(
            new ProductResource($product->load(['category', 'brand'])),
            'Produto atualizado com sucesso.'
        );
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return ApiResponse::noContent();
    }
}
