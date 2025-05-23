<?php

namespace App\Observers;

use Carbon\Carbon;
use App\Models\Controlpago;
use App\Models\RecuperacionDium;

class ControlObserver
{
    /**
     * Handle the Controlpago "created" event.
     */
    public function created(Controlpago $controlpago): void
    {
        // Ajustar la zona horaria según tus necesidades
        $fechaHoy = Carbon::now('America/Managua')->startOfDay();

        // Obtén la fecha de abono directamente desde el modelo
        $fechaAbono = Carbon::parse($controlpago->fechaContrato) // Asegúrate de que `fechaAbono` es un campo en tu modelo
            ->setTimezone('America/Managua') // Asegúrate de que la zona horaria sea correcta
            ->startOfDay(); // Asegúrate de que se compara con el inicio del día

        // Verificar si la fecha de abono es igual a la fecha de hoy
        if ($fechaAbono->isToday()) {
            // Buscar si ya existe un registro del día para la fecha actual
            $registroDia = RecuperacionDium::whereDate('created_at', $fechaHoy)->first();
            $estado = $controlpago->status == 2 ? true : false;

            if ($registroDia && $estado) {
                // Si ya existe, actualizamos el monto recolectado
                $registroDia->represtamo += $controlpago->montoPrestado;
                $registroDia->total -= $controlpago->montoPrestado;

                $registroDia->total += $controlpago->suministrado;  // Sumar el monto suministrado
                $registroDia->total -= $controlpago->gastos;  // Restar el monto gastos
                $registroDia->save();
            } elseif ($estado) {
                // Si no existe, creamos un nuevo registro para el día
                RecuperacionDium::create([
                    'represtamo' => $controlpago->montoPrestado,
                    'total' => ($registroDia ? $registroDia->montoRecolectadoDia : 0) - $controlpago->montoPrestado + $controlpago->suministrado - $controlpago->gastos,
                    'created_at' => $fechaHoy, // Guardar la fecha correcta
                ]);
            } else {
                // Si el abono no se paga, no hacemos nada
            }
        } else {
            // Si la fecha de abono no es hoy, no hacemos nada
            // Opcional: podrías lanzar un mensaje o manejar esto de alguna manera
        }
    }

    /**
     * Handle the Controlpago "updated" event.
     */
    public function updated(Controlpago $controlpago): void
    {
        // Obtener la fecha anterior y la nueva fecha del abono
        $fechaAnterior = Carbon::parse($controlpago->getOriginal('fechaContrato'))
            ->setTimezone('America/Managua')
            ->startOfDay();
        $fechaActual = Carbon::parse($controlpago->fechaContrato)
            ->setTimezone('America/Managua')
            ->startOfDay();
        $fechaHoy = Carbon::now('America/Managua')->startOfDay();
        $estado = $controlpago->status == 2 ? true : false;

        if ($fechaAnterior->eq($fechaHoy) && !$fechaActual->eq($fechaHoy) && $estado) {
            // Si el abono se cambió de hoy a otra fecha, descontamos el monto
            $this->updateControl($controlpago, 'removed');
        } elseif (!$fechaAnterior->eq($fechaHoy) && $fechaActual->eq($fechaHoy) && $estado) {
            // Si el abono se cambió de otra fecha a hoy, sumamos el monto
            $this->updateControl($controlpago, 'created');
        } elseif ($fechaAnterior->eq($fechaHoy) && $fechaActual->eq($fechaHoy) && $estado) {
            // Si la fecha sigue siendo hoy, actualizamos el monto recolectado
            $this->updateControl($controlpago, 'updated');
        }
    }

    /**
     * Handle the Controlpago "deleted" event.
     */
    public function deleted(Controlpago $controlpago): void
    {
        //
    }

    /**
     * Handle the Controlpago "restored" event.
     */
    public function restored(Controlpago $controlpago): void
    {
        //
    }

    /**
     * Handle the Controlpago "force deleted" event.
     */
    public function forceDeleted(Controlpago $controlpago): void
    {
        //
    }

    private function updateControl(Controlpago $controlpago, string $action): void
    {
        $fechaHoy = Carbon::now('America/Managua')->startOfDay();

        // Buscar si ya existe un registro del día para la fecha actual
        $registroDia = RecuperacionDium::whereDate('created_at', $fechaHoy)->first();

        if ($registroDia) {
            if ($action === 'created') {
                $registroDia->represtamo += $controlpago->montoPrestado;
                $registroDia->total  -= $controlpago->montoPrestado;
            } elseif ($action === 'removed') {
                $registroDia->represtamo -= $controlpago->montoPrestado;
                $registroDia->total += $controlpago->montoPrestado;
            } elseif ($action === 'updated') {
                // Restar el valor anterior del représtamo y el total
                $registroDia->represtamo -= $controlpago->getOriginal('montoPrestado');
                $registroDia->total += $controlpago->getOriginal('montoPrestado');  // Revertir el monto original en el total

                // Sumar el nuevo monto prestado
                $registroDia->represtamo += $controlpago->montoPrestado;
                $registroDia->total -= $controlpago->montoPrestado;  // Aplicar el nuevo monto en el total
            }

            // Guardar los cambios si el monto es positivo, si no, eliminar el registro

            $registroDia->save();

            // } else {
            //     $registroDia->delete();
            // }
        } else if ($action === 'created') {
            // Crear un nuevo registro si no existe y la acción es "created"
            RecuperacionDium::create([
                'represtamo' => $controlpago->montoPrestado,
                'total' => $registroDia->montoRecolectadoDia - $controlpago->montoPrestado,
            ]);
        }
    }
}
