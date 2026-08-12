<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'street'=>'required',
            'building'=>'required',
            'area'=>'required',
        ]);

        $location = Location::where('id', $location->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $location->street = $request->street;
        $location->building = $request->building;
        $location->area = $request->area;
        $location->update();

        return response()->json('Localização atualizada');

    }

    public function destroy(Location $location)
    {
        $location = Location::where('id', $location->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $location->delete();

        return response()->json('Localização excluída com sucesso');
    }
}
