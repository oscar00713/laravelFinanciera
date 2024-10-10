<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ControlpagoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'usuario_id' => 'required',
            'concepto' => 'required',
            'frecuencia' => 'required',
            'plazo' => 'required',
            'status' => 'required',
            'diaCobro' => 'required',
            'fechaContrato' => 'required',
            'primerCobro' => 'required',
            'mes' => 'required',
            'montoPrestado' => 'required',
            'creditoTerminado' => 'nullable',
            'interes' => 'required'
        ];
    }
    public function messages()
    {
        return [
            'usuario_id.required' => 'El campo Usuario es requerido',
            'concepto.required' => 'El campo Concepto es requerido',
            'frecuencia.required' => 'El campo Frecuencia es requerido',
            'plazo.required' => 'El campo Plazo es requerido',
            'status.required' => 'El campo Status es requerido',
            'diaCobro.required' => 'El campo Dia de Cobro es requerido',
            'fechaContrato.required' => 'El campo Fecha de Contrato es requerido',
            'primerCobro.required' => 'El campo Primer Cobro es requerido',
            'mes.required' => 'El campo Mes es requerido',
            'montoPrestado.required' => 'El campo Monto Prestado es requerido',
            'interes.required' => 'El campo Interes es requerido',
        ];
    }
}
