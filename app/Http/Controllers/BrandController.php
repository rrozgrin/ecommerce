<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Exception;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $brands = Brand::paginate(10);
        return response()->json($brands, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|unique:brands,name'
            ]);
            $brand = new Brand();
            $brand->name = $request->name;
            $brand->save();
            return response()->json('Marca adicionada com sucesso', 201);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $id)
    {
        $brand = Brand::find($id);

        if ($brand) {
            return response()->json($brand, 200);
        } else return   response()->json('Marca não encontrada');
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update_brand(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|unique:brands,name'
            ]);
            $brand = Brand::where('id', $id)->update(['name' => $request->name]);
            $brand->update();
            return response()->json('Marca atualizada com sucesso', 200);
        } catch (Exception $e) {
            return response()->json($e, 500);
        };
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy_brand(Brand $id)
    {
        $brand = Brand::find($id);
        if($brand){
            $brand->delete();
            return response()->json('Marca excluída com sucesso');
        } else return response()->json('Marca não encontrada');
    }
}