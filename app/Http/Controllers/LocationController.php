<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Http\Requests\StoreLocationRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Resources\LocationResource;
use App\Support\ApiResponse;
use Illuminate\Support\Facades\Auth;
class LocationController extends Controller
{
    public function store(StoreLocationRequest $request)
    {
        $this->authorize('create', Location::class);

        $location = Location::create($request->validated() + ['user_id' => Auth::id()]);

        return ApiResponse::created(new LocationResource($location), 'Endereço adicionado com sucesso.');
    }

    public function update(UpdateLocationRequest $request, Location $location)
    {
        $this->authorize('update', $location);

        $location->update($request->validated());

        return ApiResponse::success(new LocationResource($location), 'Endereço atualizado com sucesso.');

    }

    public function destroy(Location $location)
    {
        $this->authorize('delete', $location);

        $location->delete();

        return ApiResponse::noContent();
    }
}
