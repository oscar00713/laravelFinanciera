<?php

namespace App\Http\Controllers\historial;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class HistorialController extends Controller
{
    public function historial($usuarioId)
    {
        $usuario = User::with(['controlpagos' => function ($query) {
            $query->select('id', 'usuario_id', 'status', 'total', 'fechaContrato');
        }, 'controlpagos.abonos' => function ($query) {
            $query->select('id', 'controlpago_id', 'montoAbono', 'interesAbono', 'numAbono', 'created_at', 'estado');
        }])->find($usuarioId);

        $usuario->controlpagos->each(function ($controlpago) {
            // Sumamos los montos de los abonos
            $totalAbonado = $controlpago->abonos->sum('montoAbono');

            // Calculamos el saldo pendiente
            $controlpago->saldoPendiente = $controlpago->total - $totalAbonado;
        });
        // Devolver el historial en formato JSON
        return response()->json($usuario);
    }
}
