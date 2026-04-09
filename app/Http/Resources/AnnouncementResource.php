<?php

namespace App\Http\Resources;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin Announcement */
class AnnouncementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'message'         => $this->message,
            'attachment_path' => $this->attachment_path,
            'attachment_name' => $this->attachment_name,
            'attachment_url'  => $this->attachment_path ? Storage::disk('public')->url($this->attachment_path) : null,
            'group'           => new GroupResource($this->whenLoaded('group')),
            'created_at'      => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at'      => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}