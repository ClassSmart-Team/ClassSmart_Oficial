<?php
 
namespace App\Http\Resources;
 
use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
 
/** @mixin Chat */
class ChatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'   => $this->id,
            'name' => $this->name,
 
            // Participantes del chat
            'users'    => UserResource::collection($this->whenLoaded('users')),
            // Mensajes del chat
            'messages' => MessageResource::collection($this->whenLoaded('messages')),
 
            // Conteos útiles para el frontend
            'users_count'    => $this->whenCounted('users'),
            'messages_count' => $this->whenCounted('messages'),
 
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
 