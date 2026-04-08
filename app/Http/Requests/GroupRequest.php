<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class GroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // La autorización real se maneja en el controller
    }
 
    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'period_id'   => [$isUpdate ? 'sometimes' : 'required', 'integer', 'exists:periods,id'],
            'name'        => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:255'],
            'description' => [$isUpdate ? 'sometimes' : 'nullable', 'string'],
            'active'      => ['boolean'],
        ];
    }
 
    public function messages(): array
    {
        return [
            'period_id.required' => 'El periodo es obligatorio.',
            'period_id.exists'   => 'El periodo seleccionado no existe.',
            'name.required'      => 'El nombre del grupo es obligatorio.',
        ];
    }
}
 