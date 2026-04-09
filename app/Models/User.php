<?php

namespace App\Models;

use App\Notifications\VerifyEmailCustom;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasApiTokens, HasFactory, Notifiable, MustVerifyEmail;

    protected $fillable = [
        'name',
        'lastname',
        'email',
        'password',
        'cellphone',
        'active',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'active'            => 'boolean',
        ];
    }

    // -------------------------------------------------------
    // Rol
    // -------------------------------------------------------

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Helpers para verificar rol fácilmente
    public function isAdmin()   { return $this->role_id === 1; }
    public function isTeacher() { return $this->role_id === 2; }
    public function isStudent() { return $this->role_id === 3; }
    public function isParent()  { return $this->role_id === 4; }

    // -------------------------------------------------------
    // Relaciones para MAESTRO
    // -------------------------------------------------------

    // Grupos que imparte el maestro
    public function teachingGroups()
    {
        return $this->hasMany(Group::class, 'owner');
    }

    // -------------------------------------------------------
    // Relaciones para ALUMNO
    // -------------------------------------------------------

    // Grupos en los que está inscrito el alumno
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'student_groups', 'student_id', 'group_id')
                    ->withPivot('active')
                    ->withTimestamps();
    }

    // Entregas del alumno
    public function submissions()
    {
        return $this->hasMany(Submission::class, 'student_id');
    }

    // Calificaciones finales del alumno
    public function gradeRecords()
    {
        return $this->hasMany(GradeRecord::class, 'student_id');
    }

    // Padres del alumno
    public function parents()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'student_id', 'parent_id');
    }

    // -------------------------------------------------------
    // Relaciones para PADRE
    // -------------------------------------------------------

    // Hijos del padre
    public function children()
    {
        return $this->belongsToMany(User::class, 'parent_student', 'parent_id', 'student_id');
    }

    // -------------------------------------------------------
    // Notificaciones (todos los roles)
    // -------------------------------------------------------

    // Notificaciones recibidas con estado de lectura propio
    public function receivedNotifications()
    {
        return $this->belongsToMany(Notification::class, 'notification_user')
                    ->withPivot('read_at')
                    ->withTimestamps()
                    ->orderByDesc('created_at');
    }

    // Solo las no leídas
    public function unreadNotifications()
    {
        return $this->receivedNotifications()->wherePivotNull('read_at');
    }

    // -------------------------------------------------------
    // Chats (todos los roles)
    // -------------------------------------------------------

    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_user')
                    ->withTimestamps();
    }

    // Envia el correo de verificacion usando la plantilla personalizada.
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailCustom());
    }
}