<?php
 
namespace App\Http\Resources;
 
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
 
/** @mixin Submission */
class SubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'submission_date' => $this->submission_date->format('Y-m-d H:i:s'),
            'status'          => $this->status,
            'grade'           => $this->grade,
            'feedback'        => $this->feedback,
 
            // En lugar de IDs crudos, devolvemos los objetos completos
            'student'    => new UserResource($this->whenLoaded('student')),
            'assignment' => new AssignmentResource($this->whenLoaded('assignment')),
            'files'      => FileResource::collection($this->whenLoaded('files')),
 
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
 
