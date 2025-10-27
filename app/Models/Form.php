<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'description',
        'fields',
        'status',
        'success_message',
        'error_message',
        'email_notification',
        'notification_emails',
        'redirect_url',
    ];

    protected $casts = [
        'fields' => 'array',
        'notification_emails' => 'array',
    ];

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class);
    }

    public function getFieldTypesAttribute()
    {
        return [
            'text' => 'Text',
            'email' => 'E-Mail',
            'number' => 'Zahl',
            'select' => 'Auswahl',
            'checkbox' => 'Checkbox',
            'radio' => 'Radio',
            'textarea' => 'Textbereich',
            'file' => 'Datei',
            'date' => 'Datum',
            'time' => 'Zeit',
            'datetime' => 'Datum & Zeit',
        ];
    }
}