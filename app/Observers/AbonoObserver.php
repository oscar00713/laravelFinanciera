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
                $registroDia->montoRecolectadoDia += $abono->efectivo;
                $registroDia->ganancia += $abono->interesAbono;
                $registroDia->billetera += $abono->billetera;
                $registroDia->total += $abono->efectivo;
                $registroDia->save();
            } else {
                // Si no existe, creamos un nuevo registro para el día
                RecuperacionDium::create([
                    'montoRecolectadoDia' => $abono->efectivo,
                    'total' => $abono->efectivo,
                    'ganancia' => $abono->interesAbono,
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
        // Obtener la fecha anterior y la nueva fecha del abono
        $fechaAnterior = Carbon::parse($abono->getOriginal('fechaAbono'))
            ->setTimezone('America/Managua')
            ->startOfDay();
        $fechaActual = Carbon::parse($abono->fechaAbono)
            ->setTimezone('America/Managua')
            ->startOfDay();
        $fechaHoy = Carbon::now('America/Managua')->startOfDay();

        if ($fechaAnterior->eq($fechaHoy) && !$fechaActual->eq($fechaHoy)) {
            // Si el abono se cambió de hoy a otra fecha, descontamos el monto
            $this->updateRecuperacionDium($abono, 'removed');
        } elseif (!$fechaAnterior->eq($fechaHoy) && $fechaActual->eq($fechaHoy)) {
            // Si el abono se cambió de otra fecha a hoy, sumamos el monto
            $this->updateRecuperacionDium($abono, 'created');
        } elseif ($fechaAnterior->eq($fechaHoy) && $fechaActual->eq($fechaHoy)) {
            // Si la fecha sigue siendo hoy, actualizamos el monto recolectado
            $this->updateRecuperacionDium($abono, 'updated');
        }
    }

    /**
     * Handle the Abono "deleted" event.
     */
    public function deleted(Abono $abono): void
    {
        $fechaAbono = Carbon::parse($abono->fechaAbono)
            ->setTimezone('America/Managua')
            ->startOfDay();
        $fechaHoy = Carbon::now('America/Managua')->startOfDay();

        if ($fechaAbono->eq($fechaHoy)) {
            $this->updateRecuperacionDium($abono, 'removed');
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
    private function updateRecuperacionDium(Abono $abono, string $action): void
    {
        $fechaHoy = Carbon::now('America/Managua')->startOfDay();

        // Buscar si ya existe un registro del día para la fecha actual
        $registroDia = RecuperacionDium::whereDate('created_at', $fechaHoy)->first();

        if ($registroDia) {
            if ($action === 'created') {
                $registroDia->montoRecolectadoDia += $abono->efectivo;
                $registroDia->ganancia += $abono->interesAbono;
                $registroDia->billetera += $abono->billetera;
                $registroDia->total += $abono->efectivo;
            } elseif ($action === 'removed') {
                $registroDia->montoRecolectadoDia -= $abono->efectivo;
                $registroDia->ganancia -= $abono->interesAbono;
                $registroDia->billetera -= $abono->billetera;
                $registroDia->total -= $abono->efectivo;
            } elseif ($action === 'updated') {
                $registroDia->montoRecolectadoDia -= $abono->getOriginal('efectivo');
                $registroDia->montoRecolectadoDia += $abono->efectivo;
                $registroDia->ganancia -= $abono->getOriginal('interesAbono');
                $registroDia->ganancia += $abono->interesAbono;
                $registroDia->billetera -= $abono->getOriginal('billetera');
                $registroDia->billetera += $abono->billetera;
                $registroDia->total -= $abono->getOriginal('efectivo');
                $registroDia->total += $abono->efectivo;
            }

            // Guardar los cambios si el monto es positivo, si no, eliminar el registro
            if ($registroDia->montoRecolectadoDia > 0) {
                $registroDia->save();
            }
            // } else {
            //     $registroDia->delete();
            // }
        } else if ($action === 'created') {
            // Crear un nuevo registro si no existe y la acción es "created"
            RecuperacionDium::create([
                'montoRecolectadoDia' => $abono->efectivo,
                'ganancia' => $abono->interesAbono,
                'created_at' => $fechaHoy,
            ]);
        }
    }
}
