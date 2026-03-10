<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FileRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'submission_id' => ['required', 'integer'],
            'assignment_id' => ['required', 'integer'],
            'user_id' => ['required', 'integer'],
            'file_name' => ['required'],
            'file_path' => ['required'],
            'type' => ['required'],
            'size' => ['required', 'integer'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
