<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Support\Js;
use App\Models\Controlpago;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Requests\ControlpagoRequest;
use App\Http\Resources\ControlpagoResource;
use Illuminate\Database\Eloquent\Casts\Json;

class ControlpagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $controls = QueryBuilder::for(Controlpago::class)
            ->allowedFilters([
                AllowedFilter::callback('name', function ($query, $value) {
                    $query->whereHas('user', function ($query) use ($value) {
                        $query->where('name', 'like', "%{$value}%")
                            ->orWhere('apellido', 'like', "%{$value}%");
                    });
                }),
                AllowedFilter::exact('creditoTerminado')->default(false),
            ])
            ->with('user')  // Cargar la relación 'user'
            ->orderBy('id', 'desc')           // Ordenar la tabla
            ->jsonPaginate();

        // Modificar la estructura de la respuesta
        $controls->getCollection()->transform(function ($control) {
            return [
                'id' => $control->id,
                'user_id' => $control->usuario_id,
                'user_name' => $control->user->name,
                'apellido' => $control->user->apellido,
                'concepto' => $control->concepto,
                'frecuencia' => $control->frecuencia,
                'plazo' => $control->plazo,
                'cuotas' => $control->cuotas,
                'status' => $control->status,
                'diaCobro' => $control->diaCobro,
                'fechaContrato' => $control->fechaContrato,
                'mes' => $control->mes,
                'montoPrestado' => $control->montoPrestado,
                'interes' => $control->interes,
                'primerCobro' => $control->primerCobro,
                'cuota' => $control->cuota,
                'totalInteres' => $control->totalInteres,
                'creditoTerminado' => $control->creditoTerminado,
                'total' => $control->total,
                // Añadir otros campos que necesites
            ];
        });

        return response()->json($controls);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ControlpagoRequest $request): JsonResponse
    {
        //tomar los valores porque se crear operaciones con el control
        $validatedData = $request->validated();
        //validar que el usuario no tenga un control de pago en curso y que no este en estado de cobro

        if (Controlpago::where('usuario_id', $validatedData['usuario_id'])
            ->where('creditoTerminado', false)
            ->exists()
        ) {
            return response()->json(['error' => 'El usuario ya tiene un control de pago en curso.'], 409);
        }


        $validatedData['fechaContrato'] = Carbon::parse($validatedData['fechaContrato'])
            ->setTimezone('America/Managua') // Asegúrate de que la zona horaria sea correcta
            ->format('Y-m-d'); // Formatea la fecha a YYYY-MM-DD;
        $validatedData['primerCobro'] = Carbon::parse($validatedData['primerCobro'])
            ->setTimezone('America/Managua') // Asegúrate de que la zona horaria sea correcta
            ->format('Y-m-d'); // Formatea la fecha a YYYY-MM-DD

        if ($validatedData['frecuencia'] == '1') {
            $validatedData['cuotas'] = $validatedData['plazo'] * 4;
        } else {
            $validatedData['cuotas'] = $validatedData['plazo'] * 2;
        }
        //interes totoal
        $validatedData['totalInteres'] = ceil(($validatedData['montoPrestado'] * $validatedData['interes'] / 100) * $validatedData['plazo']);

        //calcular monto total a pagar, sacar el valor maximo de la cuota y el total de intereses
        $validatedData['total'] = ceil(($validatedData['montoPrestado'] + $validatedData['totalInteres']));
        $validatedData['cuota'] = ceil($validatedData['total'] / $validatedData['cuotas']);
        $validatedData['interes_cuota'] = ceil(($validatedData['totalInteres'] / $validatedData['total']) * 100);
        $validatedData['montoPendiente'] = $validatedData['total'];


        Controlpago::create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'El control de pago se creo correctamente.'
        ]);
        // return Controlpago::create($request->validated());
    }

    /**
     * Display the specified resource.
     */
    public function show($controlpago): JsonResponse
    {
        $controlpagoRes = Controlpago::find($controlpago);
        $controlpagoRes->load('user');

        // Transformar el registro en un array con los campos que necesitas
        $transformedControlpago = [
            'id' => $controlpagoRes->id,
            'usuario_id' => $controlpagoRes->usuario_id,
            'user_name' => $controlpagoRes->user->name,
            'apellido' => $controlpagoRes->user->apellido,
            'concepto' => $controlpagoRes->concepto,
            'frecuencia' => $controlpagoRes->frecuencia,
            'plazo' => $controlpagoRes->plazo,
            'cuotas' => $controlpagoRes->cuotas,
            'status' => $controlpagoRes->status,
            'diaCobro' => $controlpagoRes->diaCobro,
            'fechaContrato' => $controlpagoRes->fechaContrato,
            'mes' => $controlpagoRes->mes,
            'montoPrestado' => $controlpagoRes->montoPrestado,
            'interes' => $controlpagoRes->interes,
            'primerCobro' => $controlpagoRes->primerCobro,
            'cuota' => $controlpagoRes->cuota,
            'totalInteres' => $controlpagoRes->totalInteres,
            'creditoTerminado' => $controlpagoRes->creditoTerminado,
            'total' => $controlpagoRes->total,
            // Añadir otros campos que necesites
        ];

        return response()->json($transformedControlpago);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ControlpagoRequest $request, $id): Controlpago
    {
        // Validar los datos del request
        $validatedData = $request->validated();

        unset($validatedData['user_name'], $validatedData['apellido'], $validatedData['user_id'], $validatedData['id']);

        // Convertir 'fechaContrato' a una instancia de Carbon
        $validatedData['fechaContrato'] = Carbon::parse($validatedData['fechaContrato'])
            ->setTimezone('America/Managua') // Asegúrate de que la zona horaria sea correcta
            ->format('Y-m-d'); // Formatea la fecha a YYYY-MM-DD;
        $validatedData['primerCobro'] = Carbon::parse($validatedData['primerCobro'])
            ->setTimezone('America/Managua') // Asegúrate de que la zona horaria sea correcta
            ->format('Y-m-d'); // Formatea la fecha a YYYY-MM-DD

        // Calcular cuotas y primerCobro según la frecuencia
        if ($validatedData['frecuencia'] == '1') {
            $validatedData['cuotas'] = intval($validatedData['plazo']) * 4;
        } else {
            $validatedData['cuotas'] = intval($validatedData['plazo']) * 2;
        }

        // Calcular el total de intereses
        $validatedData['totalInteres'] = ($validatedData['montoPrestado'] * $validatedData['interes'] / 100) * $validatedData['plazo'];

        // Calcular el monto total a pagar
        $validatedData['total'] = $validatedData['montoPrestado'] + $validatedData['totalInteres'];

        // Calcular la cuota mensual
        $validatedData['cuota'] = $validatedData['total'] / $validatedData['cuotas'];
        $validatedData['interes_cuota'] = ceil((floatval($validatedData['totalInteres']) / floatval($validatedData['total'])) * 100);

        //dd($validatedData);
        // Actualizar el modelo con los datos calculados
        $controlpago = Controlpago::find($id);

        $controlpago->update($validatedData);

        // Devolver el modelo actualizado
        return $controlpago;
    }

    public function destroy($id): JsonResponse
    {
        $controlpago = Controlpago::find($id);

        $controlpago->delete();

        return response()->json('success', 200);
    }
}
