<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class ScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Solo el admin asigna horarios
        return $this->user()->isAdmin();
    }
 
    public function rules(): array
    {
        return [
            'group_id'   => ['required', 'integer', 'exists:groups,id'],
            'day'        => ['required', 'in:LUNES,MARTES,MIERCOLES,JUEVES,VIERNES'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['required', 'date_format:H:i', 'after:start_time'],
        ];
    }
 
    public function messages(): array
    {
        return [
            'group_id.required'  => 'El grupo es obligatorio.',
            'group_id.exists'    => 'El grupo seleccionado no existe.',
            'day.required'       => 'El día es obligatorio.',
            'day.in'             => 'El día debe ser LUNES, MARTES, MIERCOLES, JUEVES o VIERNES.',
            'start_time.required'         => 'La hora de inicio es obligatoria.',
            'start_time.date_format'      => 'La hora de inicio debe tener formato HH:MM.',
            'end_time.required'           => 'La hora de fin es obligatoria.',
            'end_time.date_format'        => 'La hora de fin debe tener formato HH:MM.',
            'end_time.after'              => 'La hora de fin debe ser posterior a la hora de inicio.',
        ];
    }
}