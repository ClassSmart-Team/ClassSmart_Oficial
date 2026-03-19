<?php
 
namespace App\Http\Resources;
 
use App\Models\GradeRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
 
/** @mixin GradeRecord */
class GradeRecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'grade' => $this->grade,
 
            // En lugar de IDs crudos, devolvemos los objetos completos
            'student' => new UserResource($this->whenLoaded('student')),
            'group'   => new GroupResource($this->whenLoaded('group')),
            'unit'    => new UnitResource($this->whenLoaded('unit')),
 
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
 