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
    $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

    return [
        'title'       => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
        'description' => [$isUpdate ? 'sometimes' : 'required', 'string'],
        'start_date'  => [$isUpdate ? 'sometimes' : 'required', 'date'],
        'end_date'    => [$isUpdate ? 'sometimes' : 'required', 'date', 'after:start_date'],
        'status'      => [$isUpdate ? 'sometimes' : 'required', 'in:Activa,Cerrada,Cancelada'],
        'group_id'    => [$isUpdate ? 'sometimes' : 'required', 'integer', 'exists:groups,id'],
        'unit_id'     => [$isUpdate ? 'sometimes' : 'required', 'integer', 'exists:units,id'],
        'files'       => ['nullable', 'array'],
        'files.*'     => ['file', 'max:10240'],
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