<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ControlpagoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'usuario_id' => $this->usuario_id, //mandar el name del usuario
            'usuario_name' => $this->user->name,
            'apellido' => $this->user->apellido,
            'concepto' => $this->concepto,
            'frecuencia' => $this->frecuencia,
            'plazo' => $this->plazo,
            'cuotas' => $this->cuotas,
            'status' => $this->status,
            'diaCobro' => $this->diaCobro,
            'fechaContrato' => $this->fechaContrato,
            'mes' => $this->mes,
            'montoPrestado' => $this->montoPrestado,
            'interes' => $this->interes,
            'primerCobro' => $this->primerCobro,
            'cuota' => $this->cuota,
            'totalInteres' => $this->totalInteres,
            'creditoTerminado' => $this->creditoTerminado,
            'total' => $this->total,
        ];
    }
}
