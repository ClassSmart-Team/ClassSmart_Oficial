<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = [
        'assignment_id',
        'student_id',
        'submission_date',
        'status',
        'grade',
        'feedback',
    ];

    protected function casts(): array
    {
        return [
            'submission_date' => 'datetime',
            'grade'           => 'decimal:2',
        ];
    }

    // Tarea a la que pertenece la entrega
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    // Alumno que hizo la entrega
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Archivos adjuntos a esta entrega
    public function files()
    {
        return $this->hasMany(File::class);
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    // ¿La entrega fue después de la fecha límite?
    public function isLate()
    {
        return $this->submission_date > $this->assignment->end_date;
    }

    // ¿Ya fue calificada?
    public function isGraded()
    {
        return $this->status === 'Calificada' && $this->grade !== null;
    }
}