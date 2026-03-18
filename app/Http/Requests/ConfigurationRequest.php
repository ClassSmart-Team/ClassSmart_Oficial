<?php
 
namespace App\Http\Requests;
 
use Illuminate\Foundation\Http\FormRequest;
 
class ConfigurationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Cualquier usuario autenticado puede cambiar sus preferencias
        return $this->user() !== null;
    }
 
    public function rules(): array
    {
        return [
            'email_notification' => ['boolean'],
            'push_notification'  => ['boolean'],
            'email_reply'        => ['boolean'],
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
 