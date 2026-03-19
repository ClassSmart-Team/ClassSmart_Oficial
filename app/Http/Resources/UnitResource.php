<?php
 
namespace App\Http\Resources;
 
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
 
/** @mixin Unit */
class UnitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'order'      => $this->order,
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date'   => $this->end_date->format('Y-m-d'),
 
            // En lugar de ID crudo, devolvemos los objetos completos
            'group'       => new GroupResource($this->whenLoaded('group')),
            'assignments' => AssignmentResource::collection($this->whenLoaded('assignments')),
            'grade_records' => GradeRecordResource::collection($this->whenLoaded('gradeRecords')),
 
            // Conteo de tareas
            'assignments_count' => $this->whenCounted('assignments'),
 
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
 