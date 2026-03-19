<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $fillable = [
        'user_id',
        'email_notification',
        'push_notification',
        'email_reply',
        'theme',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}