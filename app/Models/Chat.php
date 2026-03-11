<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'name',
    ];

    // Usuarios participantes en el chat
    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_user')
                    ->withTimestamps();
    }

    // Mensajes del chat
    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    // -------------------------------------------------------
    // Helpers
    // -------------------------------------------------------

    // ¿Es un chat 1 a 1?
    public function isPrivate()
    {
        return $this->users()->count() === 2 && $this->name === null;
    }
}