<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'owner',
        'period_id',
        'name',
        'description',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    // Maestro dueño del grupo
    public function ownerUser()
    {
        return $this->belongsTo(User::class, 'owner');
    }

    // Periodo al que pertenece el grupo
    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    // Alumnos inscritos en el grupo
    public function students()
    {
        return $this->belongsToMany(User::class, 'student_groups', 'group_id', 'student_id')
                    ->withPivot('active')
                    ->withTimestamps();
    }

    // Unidades del grupo
    public function units()
    {
        return $this->hasMany(Unit::class);
    }

    // Tareas del grupo
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    // Anuncios del grupo
    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    // Horarios del grupo
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    // Archivos del repositorio general del grupo
    public function groupFiles()
    {
        return $this->hasMany(GroupFile::class);
    }

    // Calificaciones finales del grupo
    public function gradeRecords()
    {
        return $this->hasMany(GradeRecord::class);
    }
}
