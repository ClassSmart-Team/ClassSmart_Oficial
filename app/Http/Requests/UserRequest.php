<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización real se maneja en el controller
    }
 
    public function rules(): array
    {
        // En edición (PUT/PATCH) email y password son opcionales
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
 
        return [
            'name'      => ['required', 'string', 'max:255'],
            'lastname'  => ['nullable', 'string', 'max:255'],
            'email'     => [
                $isUpdate ? 'sometimes' : 'required',
                'email',
                'max:255',
                'unique:users,email,' . $this->route('user'),
            ],
            'password'  => [$isUpdate ? 'sometimes' : 'required', 'string', 'min:6'],
            'cellphone' => ['nullable', 'string', 'max:20'],
            'active'    => [$isUpdate ? 'sometimes' : 'required', 'boolean'],
            'role_id'   => [$isUpdate ? 'sometimes' : 'required', 'integer', 'exists:roles,id'], // admin asigna el rol
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