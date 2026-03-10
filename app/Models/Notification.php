<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'message',
        'type',
        'related_group',
        'related_assignment',
        'read_status',
    ];

    protected function casts(): array
    {
        return [
            'read_status' => 'boolean',
        ];
    }
}
