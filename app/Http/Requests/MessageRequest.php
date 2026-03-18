<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class MessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Cualquier usuario autenticado puede enviar mensajes
        // excepto padres — solo pueden ver, no escribir
        return !$this->user()->isParent();
    }
 
    public function rules(): array
    {
        return [
            'chat_id' => ['required', 'integer', 'exists:chats,id'],
            'content' => ['required', 'string', 'max:2000'],
            // user_id se asigna automáticamente desde $request->user()->id en el controller
        ];
    }
 
    public function messages(): array
    {
        return [
            'chat_id.required' => 'El chat es obligatorio.',
            'chat_id.exists'   => 'El chat seleccionado no existe.',
            'content.required' => 'El mensaje no puede estar vacío.',
            'content.max'      => 'El mensaje no puede exceder 2000 caracteres.',
        ];
    }
}
 