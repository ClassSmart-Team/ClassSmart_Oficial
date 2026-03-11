<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupFile extends Model
{
    protected $fillable = [
        'group_id',
        'uploaded_by',
        'file_name',
        'file_path',
        'type',
        'size',
        'description',
    ];

    // Grupo al que pertenece el archivo
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // Usuario que subió el archivo
    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}