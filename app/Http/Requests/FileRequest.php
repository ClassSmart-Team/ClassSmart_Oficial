<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class FileRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Cualquier usuario autenticado puede subir archivos
        return $this->user() !== null;
    }
 
    public function rules(): array
    {
        return [
            // Solo uno de los dos debe venir, nunca ambos
            'submission_id' => ['nullable', 'integer', 'exists:submissions,id'],
            'assignment_id' => ['nullable', 'integer', 'exists:assignments,id'],
            'context'       => ['required', 'in:assignment_material,student_submission'],
 
            // El archivo real que sube el usuario
            // file_name, file_path, type y size los genera el backend al procesar el archivo
            'file'          => ['required', 'file', 'max:10240'], // max 10MB
        ];
    }
 
    public function messages(): array
    {
        return [
            'submission_id.exists' => 'La entrega seleccionada no existe.',
            'assignment_id.exists' => 'La tarea seleccionada no existe.',
            'context.required'     => 'El contexto del archivo es obligatorio.',
            'context.in'           => 'El contexto debe ser assignment_material o student_submission.',
            'file.required'        => 'El archivo es obligatorio.',
            'file.max'             => 'El archivo no puede pesar más de 10MB.',
        ];
    }
}