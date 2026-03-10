<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Grade_RecordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'integer'],
            'group_id' => ['required', 'integer'],
            'unit_id' => ['required', 'integer'],
            'grade' => ['required', 'numeric'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
