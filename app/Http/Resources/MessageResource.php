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
            'chat_id' => $this->chat_id,
            'content' => $this->content,

            'user' => new UserResource($this->whenLoaded('user')),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}