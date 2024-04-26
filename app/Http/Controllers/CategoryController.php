<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

use function PHPUnit\Framework\fileExists;

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
        try {
            $validated = $request->validate([
                'name' => 'required|unique:brands,name'
            ]);
            $category = new Category();
            $category->name = $request->name;
            $category->save();
            return response()->json('Categoria adicionada com sucesso', 201);
        } catch (Exception $e) {
            return response()->json($e, 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $id)
    {
        $category = Category::find($id);

        if ($category) {
            return response()->json($category, 200);
        } else return   response()->json('Categoria não encontrada');
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
    public function update_category(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|unique:brands,name',
                'image' => 'required|image'
            ]);
            $category = Category::find($id);
            if ($request->hasFile('image')) {
                $path = 'assets/uploads/category' . $category->image;
                if (File::exists($path)) {
                    File::delete($path);
                }
                $file = $request->file('image');
                $ext = $file->getClientOriginalExtension();
                $filename = time() . '.' . $ext;

                try {
                    $file->move('assets/uploads/category', $filename);
                } catch (Exception $e) {
                    dd($e);
                }
                $category->image = $filename;
            }

            $category->name = $request->name;
            $category->update();
            return response()->json('Categoria atualizada com sucesso', 200);
            
        } catch (Exception $e) {
            return response()->json($e, 500);
        };
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy_category(Category $id)
    {
        $category = Category::find($id);
        if ($category) {
            $category->delete();
            return response()->json('Categoria excluída com sucesso');
        } else return response()->json('Categoria não encontrada');
    }
}
