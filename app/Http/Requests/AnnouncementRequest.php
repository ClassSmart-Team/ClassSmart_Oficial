<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'group_id'   => [$isUpdate ? 'sometimes' : 'required', 'integer', 'exists:groups,id'],
            'title'      => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'message'    => [$isUpdate ? 'sometimes' : 'required', 'string'],
            'attachment' => ['nullable', 'file', 'max:10240'],
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