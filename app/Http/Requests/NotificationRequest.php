<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización real se maneja en el controller
    }

    public function rules(): array
    {
        return [
            'title'              => ['required', 'string', 'max:255'],
            'message'            => ['required', 'string'],
            'type'               => ['required', 'in:General,Individual'],
            'related_group'      => ['nullable', 'integer', 'exists:groups,id'],
            'related_assignment' => ['nullable', 'integer', 'exists:assignments,id'],
            // read_status ya no existe — se maneja en notification_user.read_at
            // created_by se asigna automáticamente desde $request->user()->id en el controller
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'         => 'El título es obligatorio.',
            'message.required'       => 'El mensaje es obligatorio.',
            'type.required'          => 'El tipo de notificación es obligatorio.',
            'type.in'                => 'El tipo debe ser General o Individual.',
            'related_group.exists'   => 'El grupo seleccionado no existe.',
            'related_assignment.exists' => 'La tarea seleccionada no existe.',
            'related_announcement.exists' => 'La anuncio seleccionado no existe.',
        ];
    }
}
