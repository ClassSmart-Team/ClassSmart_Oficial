<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class UnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización real se maneja en el controller
    }
 
    public function rules(): array
    {
        return [
            'group_id'   => ['required', 'integer', 'exists:groups,id'],
            'name'       => ['required', 'string', 'max:100'],
            'order'      => ['required', 'integer', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
        ];
    }
 
    public function messages(): array
    {
        return [
            'group_id.required'   => 'El grupo es obligatorio.',
            'group_id.exists'     => 'El grupo seleccionado no existe.',
            'name.required'       => 'El nombre de la unidad es obligatorio.',
            'order.required'      => 'El orden de la unidad es obligatorio.',
            'order.min'           => 'El orden debe ser mayor a 0.',
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'end_date.required'   => 'La fecha de fin es obligatoria.',
            'end_date.after'      => 'La fecha de fin debe ser posterior a la fecha de inicio.',
        ];
    }
}
 