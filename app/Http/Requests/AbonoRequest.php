<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AbonoRequest extends FormRequest
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
            'controlpago_id' => 'required',
            'fechaProximoAbono' => 'required',
            'montoAbono' => 'required',
            'numAbono' => 'required',
            'fechaAbono' => 'required',
            'interesAbono' => 'required',
            'estado' => 'required',

        ];
    }

    public function messages(): array
    {
        return [
            'usuario_id.required' => 'El Usuario es requerido',
            'controlpago_id.required' => 'El campo Control de Pago es requerido',
            'fechaProximoAbono.required' => 'El campo Fecha Proximo Abono es requerido',
            'interesAbono.required' => 'El campo interes Abonado es requerido',
            'montoAbono.required' => 'El campo Monto Abonado es requerido',
            'estado.required' => 'El campo Estado es requerido',
            'numAbono.required' => 'El campo Numero Abono es requerido',
            'fechaAbono' => 'El campo Fecha Abonado es requerido',
        ];
    }
}
