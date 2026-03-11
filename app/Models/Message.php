<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'user_id',
        'chat_id',
        'content',
    ];

    // Usuario que envió el mensaje
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Chat al que pertenece el mensaje
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }
}