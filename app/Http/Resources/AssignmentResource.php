<?php
 
namespace App\Http\Resources;
 
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
 
/** @mixin Assignment */
class AssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'start_date'  => $this->start_date->format('Y-m-d H:i:s'),
            'end_date'    => $this->end_date->format('Y-m-d H:i:s'),
            'status'      => $this->status,
 
            'group'       => new GroupResource($this->whenLoaded('group')),
            'unit'        => new UnitResource($this->whenLoaded('unit')),
 
            // Archivos adjuntos por el maestro
            'files'       => FileResource::collection($this->whenLoaded('files')),
 
            // Entregas de alumnos (útil para el maestro al revisar)
            'submissions' => SubmissionResource::collection($this->whenLoaded('submissions')),
 
            // Conteo de entregas (útil para el maestro)
            'submissions_count' => $this->whenCounted('submissions'),
 
            'created_at'  => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at'  => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
 