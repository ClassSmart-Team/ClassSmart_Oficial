<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'group_id',
        'title',
        'message',
        'attachment_path', // campo agregado en la migración corregida
        'attachment_name', // campo agregado en la migración corregida
    ];

    // Grupo al que pertenece el anuncio
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}


