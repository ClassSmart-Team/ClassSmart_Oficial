<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'created_by',       // campo agregado en la migración corregida
        'title',
        'message',
        'type',
        'related_group',
        'related_assignment',
        // 'read_status' ya no existe — se eliminó en la migración corregida
    ];

    // Quién creó la notificación (maestro, alumno o sistema)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Grupo relacionado a la notificación
    public function group()
    {
        return $this->belongsTo(Group::class, 'related_group');
    }

    // Tarea relacionada a la notificación
    public function assignment()
    {
        return $this->belongsTo(Assignment::class, 'related_assignment');
    }

    // Anuncio relacionado a notificacion
    public function announcement()
    {
        return $this->belongsTo(Announcement::class, 'related_announcement');
    }

    // Boleta relacionado a notificacion
    public function grade_records()
    {
        return $this->belongsTo(GradeRecord::class, 'related_grade_record');
    }

    // Todos los destinatarios con su estado de lectura individual
    public function recipients()
    {
        return $this->belongsToMany(User::class, 'notification_user')
            ->withPivot('read_at')
            ->withTimestamps();
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    // Marcar como leída para un usuario específico
    public function markAsReadFor($user)
    {
        $this->recipients()->updateExistingPivot($user->id, [
            'read_at' => now(),
        ]);
    }

    // Enviar notificación a los padres de un alumno
    public static function notifyParentsOf($student, array $data)
    {
        $notification = self::create($data);
        $parentIds = $student->parents()->pluck('users.id');
        $notification->recipients()->attach($parentIds, ['read_at' => null]);
        return $notification;
    }
}
