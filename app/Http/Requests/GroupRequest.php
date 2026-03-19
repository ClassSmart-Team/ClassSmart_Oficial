<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class GroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización real se maneja en el controller
    }
 
    public function rules(): array
    {
        return [
            'period_id'   => ['required', 'integer', 'exists:periods,id'],
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'active'      => ['boolean'],
        ];
    }
 
    public function messages(): array
    {
        return [
            'period_id.required' => 'El periodo es obligatorio.',
            'period_id.exists'   => 'El periodo seleccionado no existe.',
            'name.required'      => 'El nombre del grupo es obligatorio.',
        ];
    }
}
 