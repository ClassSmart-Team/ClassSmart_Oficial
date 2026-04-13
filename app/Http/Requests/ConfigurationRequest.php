<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigurationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización real se maneja en el controller
    }

    public function rules(): array
    {
        return [
            'email_notification' => ['boolean'],
            'email_new_assignments' => ['boolean'],
            'email_submissions' => ['boolean'],
            'email_grades' => ['boolean'],
            'email_feedback' => ['boolean'],
            'email_announcements' => ['boolean'],
            'email_grade_records' => ['boolean'],

            'push_notification'  => ['boolean'],
            'push_new_assignments' => ['boolean'],
            'push_submissions' => ['boolean'],
            'push_grades' => ['boolean'],
            'push_feedback' => ['boolean'],
            'push_announcements' => ['boolean'],
            'push_grade_records' => ['boolean'],

            'theme'              => ['in:light,dark'],
            // user_id se asigna automáticamente desde $request->user()->id en el controller
        ];
    }

    public function messages(): array
    {
        return [
            'theme.in' => 'El tema debe ser light o dark.',
        ];
    }
}
