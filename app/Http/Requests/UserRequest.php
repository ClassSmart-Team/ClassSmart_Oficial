<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $userId = $this->route('id') ?? $this->route('user');

        return [
            'name' => [
                $isUpdate ? 'sometimes' : 'required',
                'string',
                'max:255',
            ],

            'lastname' => [
                $isUpdate ? 'sometimes' : 'nullable',
                'nullable',
                'string',
                'max:255',
            ],

            'email' => [
                $isUpdate ? 'sometimes' : 'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            'password' => [
                $isUpdate ? 'sometimes' : 'required',
                'nullable',
                'string',
                'min:6',
            ],

            'cellphone' => [
                $isUpdate ? 'sometimes' : 'nullable',
                'nullable',
                'string',
                'max:20',
            ],

            'active' => [
                $isUpdate ? 'sometimes' : 'required',
                'boolean',
            ],

            'role_id' => [
                $isUpdate ? 'sometimes' : 'required',
                'integer',
                'exists:roles,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'     => 'El nombre es obligatorio.',
            'email.required'    => 'El correo electrónico es obligatorio.',
            'email.email'       => 'El correo electrónico debe ser una dirección válida.',
            'email.unique'      => 'Este correo electrónico ya está registrado.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min'      => 'La contraseña debe tener al menos 6 caracteres.',
            'role_id.required'  => 'El rol es obligatorio.',
            'role_id.exists'    => 'El rol seleccionado no existe.',
        ];
    }
}