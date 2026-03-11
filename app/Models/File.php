<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'submission_id',
        'assignment_id',
        'user_id',
        'context', // campo agregado en la migración corregida
        'file_name',
        'file_path',
        'type',
        'size',
    ];

    
    // Entrega a la que pertenece (si es archivo del alumno)
    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    // Tarea a la que pertenece (si es material del maestro)
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    // Usuario que subió el archivo
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    // ¿Es material del maestro?
    public function isAssignmentMaterial(): bool
    {
        return $this->context === 'assignment_material';
    }

    // ¿Es entrega del alumno?
    public function isStudentSubmission(): bool
    {
        return $this->context === 'student_submission';
    }
}
