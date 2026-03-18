<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class PeriodRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo el admin puede crear/editar periodos
        return $this->user()->isAdmin();
    }
 
    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:100'],
            'year'       => ['required', 'integer', 'min:2000', 'max:2100'],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after:start_date'],
        ];
    }
 
    public function messages(): array
    {
        return [
            'name.required'       => 'El nombre del periodo es obligatorio.',
            'year.required'       => 'El año es obligatorio.',
            'year.min'            => 'El año no parece válido.',
            'year.max'            => 'El año no parece válido.',
            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'end_date.required'   => 'La fecha de fin es obligatoria.',
            'end_date.after'      => 'La fecha de fin debe ser posterior a la fecha de inicio.',
        ];
    }
}
 