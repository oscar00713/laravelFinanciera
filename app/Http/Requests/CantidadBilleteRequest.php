<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CantidadBilleteRequest extends FormRequest
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
			'billetes10' => 'required',
			'billetes20' => 'required',
			'billetes50' => 'required',
			'billetes100' => 'required',
			'billetes200' => 'required',
			'billetes500' => 'required',
			'billetes1000' => 'required',
			'monedas1' => 'required',
			'monedas5' => 'required',
			'dolares1' => 'required',
			'dolares5' => 'required',
			'dolares10' => 'required',
			'dolares20' => 'required',
			'dolares50' => 'required',
			'dolares100' => 'required',
        ];
    }
}
