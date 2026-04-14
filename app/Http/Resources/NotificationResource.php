<?php

namespace App\Http\Resources;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Notification */
class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $roleName = strtolower($user->role->description);

        return [
            'id'      => $this->id,
            'title'   => $this->title,
            'message' => $this->message,
            'type'    => $this->type,

            // En lugar de IDs crudos, devolvemos los objetos completos
            'created_by' => new UserResource($this->whenLoaded('creator')),
            'group'      => new GroupResource($this->whenLoaded('group')),
            'assignment' => new AssignmentResource($this->whenLoaded('assignment')),

            // read_status ya no existe — el estado de lectura viene del pivot notification_user
            // null = no leída, timestamp = cuándo la leyó
            'read_at' => $this->whenPivotLoaded('notification_user', fn() => $this->pivot->read_at),
            'is_read'    => $this->pivot ? !is_null($this->pivot->read_at) : false,

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
