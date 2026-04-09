<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización real se maneja en el controller
    }
 
    public function rules(): array
{
    $roleId = $this->route('role') ?? $this->route('id');

    return [
        'description' => [
            'required',
            'string',
            'max:255',
            Rule::unique('roles', 'description')->ignore($roleId),
        ],
    ];
}
 
    public function messages(): array
    {
        return [
            'description.required' => 'El nombre del rol es obligatorio.',
            'description.unique'   => 'Ya existe un rol con ese nombre.',
            'description.max'      => 'El nombre del rol no puede exceder 255 caracteres.',
        ];
    }
}
 