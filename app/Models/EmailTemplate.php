<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'variables',
        'status',
        'type',
        'description',
    ];

    protected $casts = [
        'variables' => 'array',
    ];

    public function getAvailableVariablesAttribute()
    {
        return [
            'user_name' => 'Name des Benutzers',
            'user_email' => 'E-Mail-Adresse des Benutzers',
            'rental_name' => 'Name des Vermietungsobjekts',
            'booking_id' => 'Buchungsnummer',
            'booking_date' => 'Buchungsdatum',
            'booking_amount' => 'Buchungsbetrag',
            'company_name' => 'Name des Unternehmens',
            'support_email' => 'Support-E-Mail-Adresse',
            'website_url' => 'Website-URL',
        ];
    }
}