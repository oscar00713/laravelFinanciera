<?php

namespace App\Http\Controllers\Analisis;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Controlpago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class AnalisisController extends Controller
{
    public function obtenerAnalisisCredito(Request $request)
    {
        // Obtener las fechas del request, pero solo parsearlas si están presentes
        $fechaInicio = $request->input('fechaInicio') ? Carbon::parse($request->input('fechaInicio'))->format('Y-m-d H:i:s') : null;
        $fechaFin = $request->input('fechaFin') ? Carbon::parse($request->input('fechaFin'))->format('Y-m-d H:i:s') : null;

        // Iniciar la consulta base de ControlPago
        $analisisCredito = ControlPago::leftJoin('abonos', 'controlpagos.id', '=', 'abonos.controlpago_id')
            ->where('creditoTerminado', false);

        // Filtrar por fechas si se proporcionaron
        if ($fechaInicio && $fechaFin) {
            $analisisCredito->whereBetween('controlpagos.fechaContrato', [$fechaInicio, $fechaFin]);
        }

        // Realizar la consulta con SQLite usando strftime() para extraer el año y el mes
        $analisisCredito = $analisisCredito->selectRaw('
        strftime("%Y", controlpagos.fechaContrato) as year,
        strftime("%m", controlpagos.fechaContrato) as month,
        SUM(DISTINCT controlpagos.total) as totalPrestado,
        COALESCE(SUM(abonos.interesAbono), 0) as totalGanancia,
        COALESCE(SUM(abonos.montoAbono), 0) as totalRecuperado,
        (SUM(DISTINCT controlpagos.total) - COALESCE(SUM(abonos.montoAbono), 0)) as totalPendiente
    ')
            ->groupByRaw('strftime("%Y", controlpagos.fechaContrato), strftime("%m", controlpagos.fechaContrato)')
            ->orderByRaw('year ASC, month ASC')  // Ordenar por año y mes ascendentemente
            ->get();

        // Si no se encuentran datos, devolver un mensaje adecuado
        if ($analisisCredito->isEmpty()) {
            return response()->json(['message' => 'No data found'], 404);
        }

        // Retornar los datos en formato JSON
        return response()->json($analisisCredito);
    }

    public function frecuenciaCobr()
    {
        $pagosFrecuencia = ControlPago::groupBy('frecuencia')
            ->selectRaw('frecuencia, count(*) as total')
            ->get();

        return response()->json($pagosFrecuencia);
    }

    public function clientesReprestamos()
    {
        // 1. Clientes nuevos con control de pago pendiente
        $clientesNuevosCount = User::whereHas('controlPagos', function ($query) {
            $query->where('creditoTerminado', false);  // Tienen control de pago pendiente (no terminado)
        })->count();


        // 2. Clientes con represtamos que están pagando
        $clientesReprestamosCount = User::whereHas('controlPagos', function ($query) {
            $query->where('creditoTerminado', false) // Condición de que el crédito no esté terminado
                ->where('status', 2);              // Condición de que el status sea 2
        })->count();

        // 3. Clientes que no han hecho represtamos (prestaron pero no tienen préstamos activos)
        $clientesSinReprestamosCount = User::whereHas('controlPagos', function ($query) {
            $query->where('creditoTerminado', true);  // Tienen un crédito terminado
        })->whereDoesntHave('controlPagos', function ($query) {
            $query->where('creditoTerminado', false);  // No tienen créditos pendientes
        })->count();

        // Respuesta
        $response = [
            'clientesNuevos' => $clientesNuevosCount,
            'clientesReprestamos' => $clientesReprestamosCount,
            'clientesSinReprestamos' => $clientesSinReprestamosCount,
        ];

        return response()->json($response);
    }

    public function montoPendinteUser(Request $request)
    {
        $pagosUsuarios = ControlPago::leftJoin('abonos', 'controlpagos.id', '=', 'abonos.controlpago_id')
            ->leftJoin('users', 'controlpagos.usuario_id', '=', 'users.id')
            ->where('creditoTerminado', false);

        // Filtrar por nombre o apellido si se pasa en la solicitud
        if ($request->has('nombre')) {
            $pagosUsuarios->where('users.name', 'like', '%' . $request->nombre . '%')->orWhere('users.apellido', 'like', '%' . $request->nombre . '%');
        }


        $pagosUsuarios = $pagosUsuarios->select(
            'users.name',
            'users.apellido', // Selecciona nombre y apellido del usuario
            DB::raw('SUM(DISTINCT controlpagos.montoPrestado) as totalPrestado'),
            DB::raw('(SUM(DISTINCT controlpagos.total) - COALESCE(SUM(abonos.montoAbono), 0)) as totalPendiente'),
            DB::raw('SUM(abonos.montoAbono) as totalRecuperado')
        )
            ->groupBy('users.name', 'users.apellido', 'controlpagos.usuario_id') // Agrupa por usuario
            ->orderBy(DB::raw('MAX(controlpagos.id)'), 'desc') // Ordena por el ID más alto
            ->get();

        return response()->json($pagosUsuarios);
    }
}
