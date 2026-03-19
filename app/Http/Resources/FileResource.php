<?php
 
namespace App\Http\Resources;
 
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
 
/** @mixin File */
class FileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'context'   => $this->context,
            'file_name' => $this->file_name,
            'file_path' => $this->file_path,
            'type'      => $this->type,
            'size'      => $this->size,
 
            // En lugar de IDs crudos, devolvemos los objetos completos
            'user'       => new UserResource($this->whenLoaded('user')),
            'submission' => new SubmissionResource($this->whenLoaded('submission')),
            'assignment' => new AssignmentResource($this->whenLoaded('assignment')),
 
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
 