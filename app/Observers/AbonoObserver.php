<?php

namespace App\Observers;

use Carbon\Carbon;
use App\Models\Abono;
use App\Models\RecuperacionDium;

class AbonoObserver
{
    /**
     * Handle the Abono "created" event.
     */
    public function created(Abono $abono): void
    {
        // Obtener la fecha del abono y convertirla al timezone especificado
        $fechaHoy = Carbon::now('America/Managua')->startOfDay(); // Ajustar el timezone según tus necesidades

        // Buscar si ya existe un registro del día para la fecha actual
        $registroDia = RecuperacionDium::whereDate('created_at', $fechaHoy)->first();

        if ($registroDia) {
            // Si ya existe, actualizamos el monto recolectado
            $registroDia->montoRecolectadoDia += $abono->montoAbono;
            $registroDia->save();
        } else {
            // Si no existe, creamos un nuevo registro para el día
            RecuperacionDium::create([
                'montoRecolectadoDia' => $abono->montoAbono,
                'created_at' => $fechaHoy, // Guardar la fecha correcta
            ]);
        }
    }

    /**
     * Handle the Abono "updated" event.
     */
    public function updated(Abono $abono): void
    {
        //
    }

    /**
     * Handle the Abono "deleted" event.
     */
    public function deleted(Abono $abono): void
    {
        //
    }

    /**
     * Handle the Abono "restored" event.
     */
    public function restored(Abono $abono): void
    {
        //
    }

    /**
     * Handle the Abono "force deleted" event.
     */
    public function forceDeleted(Abono $abono): void
    {
        //
    }
}
