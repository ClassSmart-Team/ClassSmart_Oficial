<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class GroupFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización real se maneja en el controller
    }
 
    public function rules(): array
    {
        return [
            'group_id'    => ['required', 'integer', 'exists:groups,id'],
            'file'        => ['required', 'file', 'max:10240'], // max 10MB
            'description' => ['nullable', 'string', 'max:255'],
            // uploaded_by se asigna automáticamente desde $request->user()->id en el controller
        ];
    }
 
    public function messages(): array
    {
        return [
            'group_id.required' => 'El grupo es obligatorio.',
            'group_id.exists'   => 'El grupo seleccionado no existe.',
            'file.required'     => 'El archivo es obligatorio.',
            'file.max'          => 'El archivo no puede pesar más de 10MB.',
        ];
    }
}
 