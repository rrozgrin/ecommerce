<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::paginate(10);
        return response()->json($categories, 200);
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
        $validated = $request->validate([
            'name' => 'required|unique:categories,name',
        ]);

        Category::create($validated);

        return response()->json('Categoria adicionada com sucesso', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json($category, 200);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit_category(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', Rule::unique('categories', 'name')->ignore($category)],
            'image' => 'nullable|image',
        ]);

        if ($request->hasFile('image')) {
            $path = 'public/assets/uploads/category/' . $category->image;
            if (File::exists($path)) {
                File::delete($path);
            }
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move('public/assets/uploads/category', $filename);
            $category->image = $filename;
        }

        $category->name = $validated['name'];
        $category->update();

        return response()->json('Categoria atualizada com sucesso');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json('Categoria excluída com sucesso');
    }
}
