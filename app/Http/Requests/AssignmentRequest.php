<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class AssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización real se maneja en el controller
    }
 
    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'start_date'  => ['required', 'date'],
            'end_date'    => ['required', 'date', 'after:start_date'],
            'status'      => ['required', 'in:Activa,Cerrada,Cancelada'],
            'group_id'    => ['required', 'integer', 'exists:groups,id'],
            'unit_id'     => ['required', 'integer', 'exists:units,id'],
            // Archivos adjuntos opcionales (material del maestro)
            'files'          => ['nullable', 'array'],
            'files.*'        => ['file', 'max:10240'], // max 10MB por archivo
        ];
    }
 
    public function messages(): array
    {
        return [
            'title.required'      => 'El título es obligatorio.',
            'description.required'=> 'La descripción es obligatoria.',
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'end_date.required'   => 'La fecha de entrega es obligatoria.',
            'end_date.after'      => 'La fecha de entrega debe ser posterior a la fecha de inicio.',
            'status.in'           => 'El estado debe ser Activa, Cerrada o Cancelada.',
            'group_id.exists'     => 'El grupo seleccionado no existe.',
            'unit_id.exists'      => 'La unidad seleccionada no existe.',
            'files.*.max'         => 'Cada archivo no puede pesar más de 10MB.',
        ];
    }
}