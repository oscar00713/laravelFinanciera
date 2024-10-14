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
        // Ajustar la zona horaria según tus necesidades
        $fechaHoy = Carbon::now('America/Managua')->startOfDay();

        // Obtén la fecha de abono directamente desde el modelo
        $fechaAbono = Carbon::parse($abono->fechaAbono) // Asegúrate de que `fechaAbono` es un campo en tu modelo
            ->setTimezone('America/Managua') // Asegúrate de que la zona horaria sea correcta
            ->startOfDay(); // Asegúrate de que se compara con el inicio del día

        // Verificar si la fecha de abono es igual a la fecha de hoy
        if ($fechaAbono->isToday()) {
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
        } else {
            // Si la fecha de abono no es hoy, no hacemos nada
            // Opcional: podrías lanzar un mensaje o manejar esto de alguna manera
        }
    }


    /**
     * Handle the Abono "updated" event.
     */
    public function updated(Abono $abono): void
    {
        // Obtener el valor anterior y el nuevo valor de la fecha
        $fechaAnterior = Carbon::parse($abono->getOriginal('fechaAbono'))
            ->setTimezone('America/Managua')->startOfDay();
        $fechaActual = Carbon::parse($abono->fechaAbono)
            ->setTimezone('America/Managua')->startOfDay();

        // Ajustar el registro en RecuperacionDium
        if ($fechaAnterior->isToday() && !$fechaActual->isToday()) {
            // Si se cambia de hoy a otra fecha, disminuimos el monto
            $registroDia = RecuperacionDium::whereDate('created_at', $fechaAnterior)->first();
            if ($registroDia) {
                $registroDia->montoRecolectadoDia -= $abono->montoAbono;
                $registroDia->save();
            }
        } elseif (!$fechaAnterior->isToday() && $fechaActual->isToday()) {
            // Si se cambia de otra fecha a hoy, aumentamos el monto
            $this->updateRecuperacionDium($abono);
        } elseif ($fechaAnterior->isToday() && $fechaActual->isToday()) {
            // Si se actualiza la fecha pero sigue siendo hoy, solo actualizamos el monto
            $registroDia = RecuperacionDium::whereDate('created_at', $fechaActual)->first();
            if ($registroDia) {
                $registroDia->montoRecolectadoDia -= $abono->getOriginal('montoAbono');
                $registroDia->montoRecolectadoDia += $abono->montoAbono;
                $registroDia->save();
            }
        }
    }

    /**
     * Handle the Abono "deleted" event.
     */
    public function deleted(Abono $abono): void
    {
        // Obtener la fecha del abono eliminado
        $fechaAbono = Carbon::parse($abono->fechaAbono)
            ->setTimezone('America/Managua')
            ->startOfDay();

        // Buscar el registro de RecuperacionDium correspondiente
        $registroDia = RecuperacionDium::whereDate('created_at', $fechaAbono)->first();

        if ($registroDia) {
            // Reducir el monto recolectado del día por el monto del abono eliminado
            $registroDia->montoRecolectadoDia -= $abono->montoAbono;

            // Si el monto recolectado es 0 o menor, podrías considerar eliminar el registro
            if ($registroDia->montoRecolectadoDia <= 0) {
                $registroDia->delete();
            } else {
                // Guardar los cambios si el monto aún es positivo
                $registroDia->save();
            }
        }
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

    /**
     * Update the RecuperacionDium record based on today's abono.
     */
    private function updateRecuperacionDium(Abono $abono): void
    {
        // Ajustar la zona horaria según tus necesidades
        $fechaHoy = Carbon::now('America/Managua')->startOfDay();

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
}
