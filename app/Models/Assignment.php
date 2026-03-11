<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
        'group_id',
        'unit_id',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    // Grupo al que pertenece la tarea
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // Unidad a la que pertenece la tarea
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // Entregas de los alumnos para esta tarea
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    // Archivos adjuntos por el maestro a esta tarea
    public function files()
    {
        return $this->hasMany(File::class);
    }

    // Notificaciones relacionadas a esta tarea
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'related_assignment');
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    // ¿La tarea sigue aceptando entregas?
    public function isActive(): bool
    {
        return $this->status === 'Activa';
    }

    // Alumnos del grupo que NO han entregado esta tarea
    public function pendingStudents()
    {
        return $this->group->students()
            ->whereDoesntHave('submissions', fn($q) =>
                $q->where('assignment_id', $this->id)
            );
    }

}
