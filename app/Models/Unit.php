<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        'group_id',
        'name',
        'start_date',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date'   => 'date',
        ];
    }

    // Grupo al que pertenece la unidad
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // Tareas de esta unidad
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    // Calificaciones finales de esta unidad
    public function gradeRecords()
    {
        return $this->hasMany(GradeRecord::class);
    }
}
