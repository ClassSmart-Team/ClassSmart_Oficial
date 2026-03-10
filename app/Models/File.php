<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'submission_id',
        'assignment_id',
        'user_id',
        'file_name',
        'file_path',
        'type',
        'size',
    ];
}
