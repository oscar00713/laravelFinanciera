<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\RecuperacionDium;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Requests\RecuperacionDiumRequest;
use App\Http\Resources\RecuperacionDiumResource;

class RecuperacionDiumController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $data = QueryBuilder::for(RecuperacionDium::class)
            ->allowedFilters(['created_at'])
            ->orderBy('id', 'desc')           // Ordenar la tabla
            ->jsonPaginate();


        // Modificar la estructura de la respuesta
        $data->getCollection()->transform(function ($resp) {
            return [
                'id' => $resp->id,
                'montoRecolectadoDia' => $resp->montoRecolectadoDia,
                'represtamo' => $resp->represtamo,
                'descripcion' => $resp->descripcion,
                'montoCordobas' => $resp->montoCordobas,
                'montoDolares' => $resp->montoDolares,
                'gastos' => $resp->gastos,

                //pasar la fecha a formato dd/mm/aaaa


                'created_at' => Carbon::parse($resp->created_at)->format('d/m/Y'),

                // Añadir otros campos que necesites
            ];
        });
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RecuperacionDiumRequest $request): RecuperacionDium
    {
        return RecuperacionDium::create($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show($recuperacionDium): RecuperacionDium
    {

        return RecuperacionDium::find($recuperacionDium);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RecuperacionDiumRequest $request, $id): RecuperacionDium
    {
        $recuperacionDium = RecuperacionDium::find($id);
        $recuperacionDium->update($request->validated());

        return $recuperacionDium;
    }

    public function destroy(RecuperacionDium $recuperacionDium): Response
    {
        $recuperacionDium->delete();

        return response()->noContent();
    }
}
