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
                'required',
                'string',
                'email',
                Rule::unique('users')->ignore($this->id),
            ],
            'telephone' => 'required',
            'role_id' => 'required',
            'direccion' => 'required|string',
            'municipio' => 'required|string',
            'sexo' => 'required',
            'fiador' => 'required',
            'fiador_id' => 'nullable',
            'password' => 'nullable|string',
        ];
    }
    public function messages()
    {
        return [
            'email.unique' => 'El correo electrónico ya está registrado. Por favor, utiliza uno diferente.',
            'email.email' => 'Por favor, ingresa un correo electrónico válido.',
            'name.required' => 'El nombre es obligatorio.',
            'cedula.required' => 'El número de cedula es obligatorio.',
            'cedula.unique' => 'El número de cedula ya está registrado. Por favor, utiliza uno diferente.',
            'cedula.regex' => 'El formato de la cedula no es válido.',
            'cedula.unique' => 'El número de cedula ya está registrado. Por favor, utiliza uno diferente.',
            'apellido.required' => 'El apellido es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Por favor, ingresa un correo electrónico válido.',
            'telephone.required' => 'El teléfono es obligatorio.',
            'role_id.required' => 'El rol es obligatorio.',
            'direccion.required' => 'La dirección es obligatoria.',
            'municipio.required' => 'El municipio es obligatorio.',
            'sexo.required' => 'El sexo es obligatorio.',
            'fiador.required' => 'El fiador es obligatorio.',
            'fiador_id.nullable' => 'El fiador es obligatorio.',

            // Otros mensajes personalizados...
        ];
    }
}
