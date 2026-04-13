<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Registro público — cualquiera puede registrarse
        // El role_id se asigna automáticamente como Student en el controller
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'lastname'  => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:6'],
            'cellphone' => ['nullable', 'string', 'max:20'],
            // role_id NO viene del frontend — se asigna como Student (3) en el controller
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'El campo nombre es obligatorio.',
            'lastname.required'  => 'El campo apellido es obligatorio.',
            'email.required'     => 'El campo correo electrónico es obligatorio.',
            'email.email'        => 'El correo electrónico debe ser una dirección válida.',
            'email.unique'       => 'El correo electrónico ya está registrado.',
            'password.required'  => 'El campo contraseña es obligatorio.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'cellphone.string'   => 'El campo teléfono celular debe ser una cadena de texto.',
            'cellphone.max'      => 'El campo teléfono celular no puede exceder los 20 caracteres.',
        ];
    }
}
