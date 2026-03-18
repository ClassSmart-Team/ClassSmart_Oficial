<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo el admin puede crear/editar roles
        return $this->user()->isAdmin();
    }
 
    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255', 'unique:roles,description'],
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
 