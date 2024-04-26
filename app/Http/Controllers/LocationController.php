<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Locale;


use function PHPUnit\Framework\returnSelf;

class LocationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'street'=>'required',
            'building'=>'required',
            'area'=>'required',
        ]);
        
        $location = new Location();
        $location-> street = $request->street;
        $location-> building = $request->building;
        $location-> area = $request->area;
        $location-> user_id = Auth::id();
        $location-> save();
        

        return response()->json('Localização adicionada com sucesso', 201);
    }

    public function update_location(Request $request, $id)
    {
        $request->validate([
            'street'=>'required',
            'building'=>'required',
            'area'=>'required',
        ]);

        $location = Location::find($id);
        if($location){
            $location->street = $request->street;
            $location->building = $request->building;
            $location->area = $request->area;
            $location->update();
    
            return response()->json('Localização atualizada');
        } else return response()->json('Localização não encontrada');

    }

    public function delete_location($id)
    {
        $location = Location::find();
        if($location){
            $location->delete();
            return response()->json('Localização excluída com sucesso');
        }
        else return response()->json('Localização não encontrada');
        
    }
}

