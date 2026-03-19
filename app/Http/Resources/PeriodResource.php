<?php
 
namespace App\Http\Resources;
 
use App\Models\Period;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
 
/** @mixin Period */
class PeriodResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'year'       => $this->year,
            'start_date' => $this->start_date->format('Y-m-d'),
            'end_date'   => $this->end_date->format('Y-m-d'),
 
            // Grupos de este periodo
            'groups'       => GroupResource::collection($this->whenLoaded('groups')),
            'groups_count' => $this->whenCounted('groups'),
 
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
