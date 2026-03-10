<?php

namespace App\Http\Resources;

use App\Models\Grade_Record;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Grade_Record */
class Grade_RecordResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'group_id' => $this->group_id,
            'unit_id' => $this->unit_id,
            'grade' => $this->grade,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
