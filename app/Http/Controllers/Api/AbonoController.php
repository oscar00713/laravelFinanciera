<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Abono;
use Illuminate\Support\Js;
use App\Models\Controlpago;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\AbonoRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\AbonoResource;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class AbonoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $Abonos = QueryBuilder::for(Abono::class)
            ->allowedFilters([
                AllowedFilter::callback('name', function ($query, $value) {
                    $query->whereHas('user', function ($query) use ($value) {
                        $query->where('name', 'like', "%{$value}%")
                            ->orWhere('apellido', 'like', "%{$value}%");
                    });
                }),
            ])
            ->with('user', 'controlpago')  // Cargar la relación 'user'
            ->whereHas('controlpago', function ($query) {
                $query->where('creditoTerminado', false); // Filtrar abonos cuyo control de pago no esté terminado
            })
            //->orderByRaw("CASE WHEN fechaProximoAbono = CURDATE() THEN 0 ELSE 1 END ASC")
            ->orderByRaw(" CASE
             WHEN fechaProximoAbono = date('now') AND estado != 3 THEN 0  -- Prioridad 1: Fecha de hoy (excepto si estado = 3)
        WHEN estado = 2 THEN 1   -- Prioridad 0: Estado 2 (primero)

        WHEN estado = 3 THEN 2   -- Prioridad 2: Estado 3 (siempre tercero)
        ELSE 3  -- Otros casos
    END ASC")
            ->orderBy('numAbono', 'desc') // Luego por fechaProximoAbono en orden ascendente
            ->orderBy('fechaProximoAbono', 'desc')           // Ordenar la tabla
            ->jsonPaginate();

        // Modificar la estructura de la respuesta
        $Abonos->getCollection()->transform(function ($abono) {
            return [
                'id' => $abono->id,
                'user_id' => $abono->usuario_id,
                'user_name' => $abono->user->name,
                'apellido' => $abono->user->apellido,
                'controlpago_id' => $abono->controlpago_id,
                'controlpago_total' => $abono->controlpago->total,
                'numAbono' => $abono->numAbono,
                'fechaProximoAbono' => $abono->fechaProximoAbono,
                'fechaAbono' => $abono->fechaAbono,
                'estado' => $abono->estado,
                'montoAbonado' => $abono->montoAbono,
                'interesAbono' => $abono->interesAbono,
                'capital' => $abono->total,
                // Añadir otros campos que necesites
            ];
        });

        return response()->json($Abonos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AbonoRequest $request): Abono
    {
        // Validar los datos del request
        $validatedData = $request->validated();

        $controlPago = Controlpago::find($validatedData['controlpago_id']);

        if ($controlPago->creditoTerminado) {
            return response()->json(['error' => 'No se pueden hacer abonos a un crédito terminado.'], 400);
        }

        // Convertir 'fechaContrato' a una instancia de Carbon
        $validatedData['fechaProximoAbono'] = Carbon::parse($validatedData['fechaProximoAbono'])
            ->setTimezone('America/Managua') // Asegúrate de que la zona horaria sea correcta
            ->format('Y-m-d'); // Formatea la fecha a YYYY-MM-DD;
        $validatedData['fechaAbono'] = Carbon::parse($validatedData['fechaAbono'])
            ->setTimezone('America/Managua') // Asegúrate de que la zona horaria sea correcta
            ->format('Y-m-d'); // Formatea la fecha a YYYY-MM-DD;

        $validatedData['total'] =  $validatedData['montoAbono'] - $validatedData['interesAbono'];

        return Abono::create($validatedData);
    }

    /**
     * Display the specified resource.
     */
    public function show($abono): JsonResponse
    {
        $abonoRes = Abono::find($abono);
        $abonoRes->load('user', 'controlpago');

        // Transformar el registro en un array con los campos que necesitas
        $transformedAbono = [
            'id' => $abonoRes->id,
            'usuario_id' => $abonoRes->usuario_id,
            'user_name' => $abonoRes->user->name,
            'apellido' => $abonoRes->user->apellido,
            'controlpago_id' => $abonoRes->controlpago_id,
            'controlpago_total' => $abonoRes->controlpago->total,
            'controlpago_interes_cuota' => $abonoRes->controlpago->interes_cuota,
            'numAbono' => $abonoRes->numAbono,
            'fechaProximoAbono' => $abonoRes->fechaProximoAbono,
            'estado' => $abonoRes->estado,
            'fechaAbono' => $abonoRes->fechaAbono,
            'montoAbono' => $abonoRes->montoAbono,
            'interesAbono' => $abonoRes->interesAbono,
            'capital' => $abonoRes->total,
            // Añadir otros campos que necesites
        ];

        return response()->json($transformedAbono);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AbonoRequest $request, $id): JsonResponse
    {
        $validatedData = $request->validated();

        $validatedData['fechaProximoAbono'] = Carbon::parse($validatedData['fechaProximoAbono'])
            ->setTimezone('America/Managua') // Asegúrate de que la zona horaria sea correcta
            ->format('Y-m-d'); // Formatea la fecha a YYYY-MM-DD;

        $validatedData['fechaAbono'] = Carbon::parse($validatedData['fechaAbono'])
            ->setTimezone('America/Managua') // Asegúrate de que la zona horaria sea correcta
            ->format('Y-m-d'); // Formatea la fecha a YYYY-MM-DD;

        $validatedData['total'] =  $validatedData['montoAbono'] - $validatedData['interesAbono'];

        $abono = Abono::find($id);

        $abono->update($validatedData);
        return response()->json('success', 200);
    }

    public function destroy(Abono $abono): Response
    {
        $abono->delete();

        return response()->noContent();
    }
}
