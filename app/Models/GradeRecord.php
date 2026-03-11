<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeRecord extends Model
{
    protected $fillable = [
        'student_id',
        'group_id',
        'unit_id',
        'grade',
    ];

    // Alumno al que pertenece la calificación
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    // Grupo al que pertenece la calificación
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    // Unidad a la que pertenece la calificación
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    // Calcula el promedio de entregas de un alumno en una unidad/grupo
    public static function calcularPromedio(int $studentId, int $groupId, int $unitId): float
    {
        return Submission::whereHas('assignment', fn($q) =>
                $q->where('group_id', $groupId)
                  ->where('unit_id', $unitId)
            )
            ->where('student_id', $studentId)
            ->whereNotNull('grade') // solo entregas ya calificadas
            ->avg('grade') ?? 0;
    }

    // Calcula y guarda el promedio final de la unidad para un alumno
    // Uso: GradeRecord::cerrarUnidad($alumno->id, $grupo->id, $unidad->id)
    public static function cerrarUnidad(int $studentId, int $groupId, int $unitId): self
    {
        $promedio = self::calcularPromedio($studentId, $groupId, $unitId);

        return self::updateOrCreate(
            [
                'student_id' => $studentId,
                'group_id'   => $groupId,
                'unit_id'    => $unitId,
            ],
            ['grade' => $promedio]
        );
    }
}
