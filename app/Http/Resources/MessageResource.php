<?php
 
namespace App\Http\Resources;
 
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
 
/** @mixin Message */
class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'content' => $this->content,
 
            // En lugar de IDs crudos, devolvemos los objetos completos
            'user' => new UserResource($this->whenLoaded('user')),
            'chat' => new ChatResource($this->whenLoaded('chat')),
 
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
 