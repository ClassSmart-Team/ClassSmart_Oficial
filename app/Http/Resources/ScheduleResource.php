<?php
 
namespace App\Http\Resources;
 
use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
 
/** @mixin Schedule */
class ScheduleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'day'        => $this->day,
            'start_time' => $this->start_time,
            'end_time'   => $this->end_time,
 
            // En lugar de ID crudo, devolvemos el objeto completo
            'group' => new GroupResource($this->whenLoaded('group')),
 
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
 