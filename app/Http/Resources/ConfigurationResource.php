<?php
 
namespace App\Http\Resources;
 
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
 
/** @mixin Configuration */
class ConfigurationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'email_notification' => $this->email_notification,
            'push_notification'  => $this->push_notification,
            'email_reply'        => $this->email_reply,
            'theme'              => $this->theme,
 
            // En lugar de user_id crudo, devolvemos el objeto completo
            'user' => new UserResource($this->whenLoaded('user')),
 
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}