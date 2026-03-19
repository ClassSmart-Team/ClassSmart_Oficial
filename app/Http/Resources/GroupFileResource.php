<?php
 
namespace App\Http\Resources;
 
use App\Models\GroupFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
 
/** @mixin GroupFile */
class GroupFileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'file_name'   => $this->file_name,
            'file_path'   => $this->file_path,
            'type'        => $this->type,
            'size'        => $this->size,
            'description' => $this->description,
 
            // En lugar de IDs crudos, devolvemos los objetos completos
            'group'       => new GroupResource($this->whenLoaded('group')),
            'uploaded_by' => new UserResource($this->whenLoaded('uploadedBy')),
 
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
 