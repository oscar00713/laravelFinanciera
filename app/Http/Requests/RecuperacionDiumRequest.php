<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecuperacionDiumRequest extends FormRequest
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
            'montoCordobas' => 'nullable',
            'montoRecolectadoDia' => 'required',
            'represtamo' => 'nullable',
            'descripcion' => 'nullable',
            'montoDolares' => 'nullable',
            'gastos' => 'required',
            'ganancia' => 'nullable',
            'suministrado' => 'nullable',
            'billetera' => 'nullable',

        ];
    }
}
