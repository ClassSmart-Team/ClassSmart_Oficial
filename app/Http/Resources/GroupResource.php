<?php
 
namespace App\Http\Resources;
 
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
 
/** @mixin Group */
class GroupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'active'      => $this->active,
 
            // En lugar de IDs crudos, devolvemos los objetos completos
            'owner'  => new UserResource($this->whenLoaded('ownerUser')),
            'period' => new PeriodResource($this->whenLoaded('period')),
 
            // Relaciones opcionales según el contexto
            'units'       => UnitResource::collection($this->whenLoaded('units')),
            'students'    => UserResource::collection($this->whenLoaded('students')),
            'assignments' => AssignmentResource::collection($this->whenLoaded('assignments')),
            'schedules'   => ScheduleResource::collection($this->whenLoaded('schedules')),
 
            // Conteos útiles para el frontend
            'students_count'    => $this->whenCounted('students'),
            'assignments_count' => $this->whenCounted('assignments'),
 
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
 