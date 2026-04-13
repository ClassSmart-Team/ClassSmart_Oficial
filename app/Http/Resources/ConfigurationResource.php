<?php

namespace App\Http\Resources;

use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Configuration */
class ConfigurationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'email_notification' => (bool)$this->email_notification,
            'email_new_assignments' => (bool)$this->email_new_assignments,
            'email_submissions' => (bool)$this->email_submissions,
            'email_grades' => (bool)$this->email_grades,
            'email_feedback' => (bool)$this->email_feedback,
            'email_announcements' => (bool)$this->email_announcements,
            'email_grade_records' => (bool)$this->email_grade_records,

            'push_notification' => (bool)$this->push_notification,
            'push_new_assignments' => (bool)$this->push_new_assignments,
            'push_submissions' => (bool)$this->push_submissions,
            'push_grades' => (bool)$this->push_grades,
            'push_feedback' => (bool)$this->push_feedback,
            'push_announcements' => (bool)$this->push_announcements,
            'push_grade_records' => (bool)$this->push_grade_records,

            'theme'              => $this->theme,

            // En lugar de user_id crudo, devolvemos el objeto completo
            'user' => new UserResource($this->whenLoaded('user')),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
