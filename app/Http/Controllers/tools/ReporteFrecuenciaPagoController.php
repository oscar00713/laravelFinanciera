<?php

namespace App\Http\Controllers\tools;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Controlpago;
use App\Models\User;
use App\Models\Abono;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteFrecuenciaPagoController extends Controller
{
    /**
     * Obtiene la lista de usuarios según la frecuencia de pago
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByFrecuencia(Request $request)
    {
        $request->validate([
            'frecuencia' => 'required|in:1,2', // 1: semanal, 2: quincenal
        ]);

        $frecuencia = $request->input('frecuencia');

        $usuarios = User::whereHas('controlpagos', function($query) use ($frecuencia) {
            $query->where('frecuencia', $frecuencia)
                  ->where('creditoTerminado', false);
        })
        ->with(['controlpagos' => function($query) {
            // Traer todos los controles de pago para calcular el ciclo correctamente
            $query->orderBy('created_at', 'asc');
        }])
        ->get();

        // Agregar información adicional relevante
        $usuarios->each(function($usuario) use ($frecuencia) {
            // Calcular el ciclo basado en todos los controles de pago
            $usuario->ciclos = $usuario->controlpagos->count();
            
            // Filtrar solo los controles de pago con la frecuencia solicitada y no terminados
            $controlesActivos = $usuario->controlpagos->filter(function($control) use ($frecuencia) {
                return $control->frecuencia == $frecuencia && !$control->creditoTerminado;
            });
            
            // Reemplazar la colección de controles con solo los activos de la frecuencia solicitada
            $usuario->setRelation('controlpagos', $controlesActivos);
            
            // Cargar los abonos para cada control activo
            $usuario->controlpagos->each(function($controlpago) {
                $controlpago->load('abonos');
                
                // Calcular monto total abonado
                $totalAbonado = $controlpago->abonos->sum('montoAbono');
                
                // Calcular saldo pendiente
                $controlpago->saldoPendiente = $controlpago->total - $totalAbonado;
                
                // Calcular próxima fecha de pago
                $ultimoAbono = $controlpago->abonos->sortByDesc('fechaAbono')->first();
                if ($ultimoAbono) {
                    $fechaUltimoAbono = Carbon::parse($ultimoAbono->fechaAbono);
                    if ($controlpago->frecuencia == '1') { // Semanal
                        $controlpago->proximoPago = $fechaUltimoAbono->addWeek()->format('Y-m-d');
                    } else { // Quincenal
                        $controlpago->proximoPago = $fechaUltimoAbono->addDays(15)->format('Y-m-d');
                    }
                } else {
                    $controlpago->proximoPago = $controlpago->primerCobro;
                }
                
                // Calcular porcentaje de avance del pago
                $controlpago->porcentajePagado = $totalAbonado > 0 ? 
                    round(($totalAbonado / $controlpago->total) * 100, 2) : 0;
            });
        });

        return response()->json([
            'success' => true,
            'data' => $usuarios,
            'total' => $usuarios->count(),
            'message' => 'Usuarios con frecuencia de pago ' . 
                         ($frecuencia == '1' ? 'semanal' : 'quincenal') . ' obtenidos correctamente'
        ]);
    }

    /**
     * Obtiene la lista de usuarios que pagaron en una fecha específica
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByFechaPago(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date_format:Y-m-d',
        ]);
    
        $fecha = $request->input('fecha');
        
        // Buscar controles de pago que tienen abonos en la fecha especificada
        $controles = Controlpago::whereHas('abonos', function($q) use ($fecha) {
            $q->whereDate('fechaAbono', $fecha);
        })
        ->with(['user', 'abonos' => function($query) use ($fecha) {
            $query->whereDate('fechaAbono', $fecha);
        }])
        ->get();
    
        // Agregar información adicional relevante SOLO del abono de la fecha
        $controles->each(function($controlpago) use ($fecha) {
            // Solo considerar el abono de la fecha
            $abonoDeLaFecha = $controlpago->abonos->first();

            $montoAbonado = $abonoDeLaFecha ? $abonoDeLaFecha->montoAbono : 0;

            // Calcular saldo pendiente después de ese abono
            $totalAbonadoHastaLaFecha = Abono::where('controlpago_id', $controlpago->id)
                ->whereDate('fechaAbono', '<=', $fecha)
                ->sum('montoAbono');
            $controlpago->saldoPendiente = $controlpago->total - $totalAbonadoHastaLaFecha;

            // Calcular porcentaje de avance del pago hasta esa fecha
            $controlpago->porcentajePagado = $totalAbonadoHastaLaFecha > 0 ? 
                round(($totalAbonadoHastaLaFecha / $controlpago->total) * 100, 2) : 0;

            // Agregar información del usuario
            $controlpago->nombreCompleto = $controlpago->user->name . ' ' . ($controlpago->user->apellido ?? '');
            $controlpago->telefono = $controlpago->user->telephone ?? '';
            $controlpago->direccion = $controlpago->user->direccion ?? '';

            // Calcular próxima fecha de pago a partir del abono de la fecha
            if ($abonoDeLaFecha) {
                $fechaUltimoAbono = Carbon::parse($abonoDeLaFecha->fechaAbono);
                if ($controlpago->frecuencia == '1') { // Semanal
                    $controlpago->proximoPago = $fechaUltimoAbono->addWeek()->format('Y-m-d');
                } else { // Quincenal
                    $controlpago->proximoPago = $fechaUltimoAbono->addDays(15)->format('Y-m-d');
                }
            } else {
                $controlpago->proximoPago = $controlpago->primerCobro;
            }
        });
    
        return response()->json([
            'success' => true,
            'data' => $controles,
            'total' => $controles->count(),
            'totalRecaudado' => $controles->sum(function($control) {
                // Solo sumar el monto del abono de la fecha
                return $control->abonos->sum('montoAbono');
            }),
            'message' => 'Clientes que pagaron en la fecha ' . $fecha . ' obtenidos correctamente'
        ]);
    }

    /**
     * Obtiene reportes con filtros combinados
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $frecuencia = $request->input('frecuencia');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');
        $status = $request->input('status');

        $query = Controlpago::with(['user' => function($query) {
            $query->withCount('controlpagos'); // Contar todos los controles de pago para el ciclo
        }, 'abonos']);

        // Aplicar filtros si están presentes
        if ($frecuencia) {
            $query->where('frecuencia', $frecuencia);
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Por defecto, mostrar solo créditos activos
        if (!$request->has('incluir_terminados')) {
            $query->where('creditoTerminado', false);
        }

        // Filtrar por rango de fechas de abono si se proporcionan
        if ($fechaInicio && $fechaFin) {
            $query->whereHas('abonos', function($q) use ($fechaInicio, $fechaFin) {
                $q->whereDate('fechaAbono', '>=', $fechaInicio)
                  ->whereDate('fechaAbono', '<=', $fechaFin);
            });
        }

        $controles = $query->get();

        // Agregar información adicional relevante
        $controles->each(function($control) {
            // Calcular monto total abonado
            $totalAbonado = $control->abonos->sum('montoAbono');
            
            // Calcular saldo pendiente
            $control->saldoPendiente = $control->total - $totalAbonado;
            
            // Calcular próxima fecha de pago
            $ultimoAbono = $control->abonos->sortByDesc('fechaAbono')->first();
            if ($ultimoAbono) {
                $fechaUltimoAbono = Carbon::parse($ultimoAbono->fechaAbono);
                if ($control->frecuencia == '1') { // Semanal
                    $control->proximoPago = $fechaUltimoAbono->addWeek()->format('Y-m-d');
                } else { // Quincenal
                    $control->proximoPago = $fechaUltimoAbono->addDays(15)->format('Y-m-d');
                }
            } else {
                $control->proximoPago = $control->primerCobro;
            }
            
            // Calcular porcentaje de avance del pago
            $control->porcentajePagado = $totalAbonado > 0 ? 
                round(($totalAbonado / $control->total) * 100, 2) : 0;
                
            // Agregar información del usuario
            $control->nombreCompleto = $control->user->name . ' ' . ($control->user->apellido ?? '');
            $control->telefono = $control->user->telephone ?? '';
            $control->direccion = $control->user->direccion ?? '';
            
            // Agregar el ciclo calculado
            $control->ciclo = $control->user->controlpagos_count;
        });

        // Estadísticas generales
        $estadisticas = [
            'totalClientes' => $controles->pluck('usuario_id')->unique()->count(),
            'totalCreditos' => $controles->count(),
            'montoTotalPrestado' => $controles->sum('montoPrestado'),
            'montoTotalPendiente' => $controles->sum('saldoPendiente'),
            'montoTotalAbonado' => $controles->sum(function($control) {
                return $control->abonos->sum('montoAbono');
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $controles,
            'estadisticas' => $estadisticas,
            'total' => $controles->count(),
            'message' => 'Reporte generado correctamente'
        ]);
    }
}
