<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'lastname'  => $this->lastname,
            'email'     => $this->email,
            'cellphone' => $this->cellphone,
            'active'    => $this->active,
            'configuration' => $this->whenLoaded('configuration'),

            // En lugar de role_id crudo, devolvemos el objeto completo
            'role' => new RoleResource($this->whenLoaded('role')),

            // Relaciones opcionales según el contexto
            'groups'       => GroupResource::collection($this->whenLoaded('groups')),
            'children'     => UserResource::collection($this->whenLoaded('children')),
            'parents'      => UserResource::collection($this->whenLoaded('parents')),

            // password, remember_token y otros campos sensibles
            // NO se incluyen aquí — ya están en $hidden del modelo

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
