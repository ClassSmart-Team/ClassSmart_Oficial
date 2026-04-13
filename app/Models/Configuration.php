<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $fillable = [
        'user_id',
        'email_notification',
        'email_new_assignments',
        'email_submissions',
        'email_grades',
        'email_feedback', '
        email_announcements',
        'email_grade_records',

        'push_notification',
        'push_new_assignments',
        'push_submissions',
        'push_grades',
        'push_feedback',
        'push_announcements',
        'push_grade_records',

        'theme',
    ];

    protected $casts = [
        'email_notification' => 'boolean',
        'push_notification'  => 'boolean',
        'email_new_assignments' => 'boolean',
        'email_submissions' => 'boolean',
        'email_grades' => 'boolean',
        'email_feedback' => 'boolean',
        'email_announcements' => 'boolean',
        'email_grade_records' => 'boolean',
        'push_new_assignments' => 'boolean',
        'push_submissions' => 'boolean',
        'push_grades' => 'boolean',
        'push_feedback' => 'boolean',
        'push_announcements' => 'boolean',
        'push_grade_records' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
