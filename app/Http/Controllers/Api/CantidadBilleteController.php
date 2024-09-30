<?php

namespace App\Http\Controllers\Api;

use App\Models\CantidadBillete;
use Illuminate\Http\Request;
use App\Http\Requests\CantidadBilleteRequest;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Resources\CantidadBilleteResource;

class CantidadBilleteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cantidadBilletes = CantidadBillete::paginate();

        return CantidadBilleteResource::collection($cantidadBilletes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CantidadBilleteRequest $request): CantidadBillete
    {
        return CantidadBillete::create($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show(CantidadBillete $cantidadBillete): CantidadBillete
    {
        return $cantidadBillete;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CantidadBilleteRequest $request, CantidadBillete $cantidadBillete): CantidadBillete
    {
        $cantidadBillete->update($request->validated());

        return $cantidadBillete;
    }

    public function destroy(CantidadBillete $cantidadBillete): Response
    {
        $cantidadBillete->delete();

        return response()->noContent();
    }
}
