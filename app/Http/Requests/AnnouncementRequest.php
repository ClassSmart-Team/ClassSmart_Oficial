<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class AnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización real se maneja en el controller
    }
 
    public function rules(): array
    {
        return [
            'group_id'        => ['required', 'integer', 'exists:groups,id'],
            'title'           => ['required', 'string', 'max:255'],
            'message'         => ['required', 'string'],
            'attachment'      => ['nullable', 'file', 'max:10240'], // max 10MB
        ];
    }
 
    public function messages(): array
    {
        return [
            'group_id.required' => 'El grupo es obligatorio.',
            'group_id.exists'   => 'El grupo seleccionado no existe.',
            'title.required'    => 'El título es obligatorio.',
            'message.required'  => 'El mensaje es obligatorio.',
            'attachment.max'    => 'El archivo no puede pesar más de 10MB.',
        ];
    }
}
 