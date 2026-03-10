<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NotificationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required'],
            'message' => ['required'],
            'type' => ['required'],
            'related_group' => ['required', 'integer'],
            'related_assignment' => ['required', 'integer'],
            'read_status' => ['boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
