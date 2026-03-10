<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Grade_Record extends Model
{
    protected $fillable = [
        'student_id',
        'group_id',
        'unit_id',
        'grade',
    ];
}
