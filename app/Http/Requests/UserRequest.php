<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Validator;

class UserRequest extends FormRequest
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
            'name' => 'required|string',
            'apellido' => 'required|string',
            'cedula' => [
                'required',
                'string',
                'regex:/^\d{3}\d{6}\d{4}[A-Za-z]$/',
                Rule::unique('users')->ignore($this->id),
            ],
            'email' => [
                'nullable',
                'email',
                Rule::unique('users')->ignore($this->id),
            ],
            'telephone' => 'required',
            'telefono' => 'nullable',
            'ciclos' => 'nullable',
            'role_id' => 'required',
            'direccion' => 'required|string',
            'municipio' => 'required|string',
            'sexo' => 'nullable',
            'fiador' => 'nullable',
            'fiador_id' => 'nullable',
            'password' => 'nullable|string',
            'activo' => 'nullable',
        ];
    }
    public function messages()
    {
        return [
            'email.email' => 'Por favor, ingresa un correo electrónico válido.',
            'name.required' => 'El nombre es obligatorio.',
            'cedula.required' => 'El número de cedula es obligatorio.',
            'cedula.unique' => 'El número de cedula ya está registrado. Por favor, utiliza uno diferente.',
            'cedula.regex' => 'El formato de la cedula no es válido.',
            'cedula.unique' => 'El número de cedula ya está registrado. Por favor, utiliza uno diferente.',
            'apellido.required' => 'El apellido es obligatorio.',
            'telephone.required' => 'El teléfono es obligatorio.',
            'role_id.required' => 'El rol es obligatorio.',
            'direccion.required' => 'La dirección es obligatoria.',
            'municipio.required' => 'El municipio es obligatorio.',

            // Otros mensajes personalizados...
        ];
    }
}
