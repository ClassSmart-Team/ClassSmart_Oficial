<?php

namespace App\Http\Resources;

use App\Models\Audit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Audit */
class AuditResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'action' => $this->action,
            'description' => $this->description,
            'entity_type' => $this->entity_type,
            'entity_id' => $this->entity_id,
            'actor' => [
                'id' => $this->actor?->id,
                'name' => $this->actor?->name,
                'lastname' => $this->actor?->lastname,
                'email' => $this->actor?->email,
                'role_id' => $this->actor?->role_id,
            ],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
