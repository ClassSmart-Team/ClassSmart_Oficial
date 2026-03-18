<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class AnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo maestros y admins pueden crear anuncios
        return $this->user()->isTeacher() || $this->user()->isAdmin();
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
 