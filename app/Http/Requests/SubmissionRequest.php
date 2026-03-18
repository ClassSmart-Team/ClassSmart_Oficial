<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class SubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo alumnos pueden entregar tareas
        return $this->user()->isStudent();
    }
 
    public function rules(): array
    {
        return [
            'assignment_id' => ['required', 'integer', 'exists:assignments,id'],
            // files es opcional — el alumno puede entregar sin archivo (comentario, texto, etc.)
            'files'         => ['nullable', 'array'],
            'files.*'       => ['file', 'max:10240'], // max 10MB por archivo
            // student_id y submission_date se asignan automáticamente en el controller
        ];
    }
 
    public function messages(): array
    {
        return [
            'assignment_id.required' => 'La tarea es obligatoria.',
            'assignment_id.exists'   => 'La tarea seleccionada no existe.',
            'files.*.max'            => 'Cada archivo no puede pesar más de 10MB.',
        ];
    }
}