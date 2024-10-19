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
                'suministrado' => $resp->suministrado,
                'billetera' => $resp->billetera,
                'total' => $resp->total,
                'ganancia' => $resp->ganancia,
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

        if (!$recuperacionDium) {
            abort(404, 'Recuperación no encontrada.');
        }

        $validatedData = $request->validated();

        // Iniciar el valor del total con el total actual
        $validatedData['total'] = floatval($recuperacionDium->total);

        // Manejar cambios en 'suministrado'
        if (isset($validatedData['suministrado']) && $recuperacionDium->getOriginal('suministrado') != $validatedData['suministrado']) {
            // Restar el valor anterior y sumar el nuevo
            $validatedData['total'] -= floatval($recuperacionDium->getOriginal('suministrado'));
            $validatedData['total'] += floatval($validatedData['suministrado']);
        }

        // Manejar cambios en 'gastos'
        if (isset($validatedData['gastos']) && $recuperacionDium->getOriginal('gastos') != $validatedData['gastos']) {
            // Sumar el valor anterior y restar el nuevo
            $validatedData['total'] += floatval($recuperacionDium->getOriginal('gastos'));
            $validatedData['total'] -= floatval($validatedData['gastos']);
        }

        // Actualizar el registro con los datos validados
        $recuperacionDium->update($validatedData);

        return $recuperacionDium;
    }

    public function destroy($id): Response
    {
        $recuperacionDium = RecuperacionDium::find($id);

        $recuperacionDium->delete();

        return response()->noContent();
    }
}
