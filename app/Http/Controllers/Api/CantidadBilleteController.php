<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\CantidadBillete;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Casts\Json;
use App\Http\Requests\CantidadBilleteRequest;
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
    public function show($abono): JsonResponse
    {

        $cantidadBilleteRes = CantidadBillete::find($abono);

        return response()->json($cantidadBilleteRes);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CantidadBilleteRequest $request, $id): CantidadBillete
    {
        $cantidadBillete = CantidadBillete::find($id);
        $cantidadBillete->update($request->validated());


        return $cantidadBillete;
    }

    public function destroy(CantidadBillete $cantidadBillete): Response
    {
        $cantidadBillete->delete();

        return response()->noContent();
    }
}
