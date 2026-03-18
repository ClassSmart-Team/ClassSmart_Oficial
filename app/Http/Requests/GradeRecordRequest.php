<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class GradeRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo maestros y admins pueden registrar calificaciones finales
        return $this->user()->isTeacher() || $this->user()->isAdmin();
    }
 
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer', 'exists:users,id'],
            'group_id'   => ['required', 'integer', 'exists:groups,id'],
            'unit_id'    => ['required', 'integer', 'exists:units,id'],
            'grade'      => ['required', 'numeric', 'min:0', 'max:10'],
        ];
    }
 
    public function messages(): array
    {
        return [
            'student_id.required' => 'El alumno es obligatorio.',
            'student_id.exists'   => 'El alumno seleccionado no existe.',
            'group_id.exists'     => 'El grupo seleccionado no existe.',
            'unit_id.exists'      => 'La unidad seleccionada no existe.',
            'grade.required'      => 'La calificación es obligatoria.',
            'grade.numeric'       => 'La calificación debe ser un número.',
            'grade.min'           => 'La calificación no puede ser menor a 0.',
            'grade.max'           => 'La calificación no puede ser mayor a 10.',
        ];
    }
}
 