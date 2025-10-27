<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Form;

class FormSeeder extends Seeder
{
    public function run(): void
    {
        $forms = [
            [
                'name' => 'Kontaktformular',
                'type' => 'contact',
                'description' => 'Standard-Kontaktformular für allgemeine Anfragen',
                'fields' => [
                    [
                        'name' => 'name',
                        'type' => 'text',
                        'required' => true
                    ],
                    [
                        'name' => 'email',
                        'type' => 'email',
                        'required' => true
                    ],
                    [
                        'name' => 'subject',
                        'type' => 'text',
                        'required' => true
                    ],
                    [
                        'name' => 'message',
                        'type' => 'textarea',
                        'required' => true
                    ]
                ],
                'status' => 'active'
            ],
            [
                'name' => 'Vermietungsanfrage',
                'type' => 'request',
                'description' => 'Formular für Vermietungsanfragen',
                'fields' => [
                    [
                        'name' => 'company_name',
                        'type' => 'text',
                        'required' => true
                    ],
                    [
                        'name' => 'contact_person',
                        'type' => 'text',
                        'required' => true
                    ],
                    [
                        'name' => 'email',
                        'type' => 'email',
                        'required' => true
                    ],
                    [
                        'name' => 'phone',
                        'type' => 'text',
                        'required' => true
                    ],
                    [
                        'name' => 'rental_period',
                        'type' => 'select',
                        'required' => true,
                        'options' => ['Täglich', 'Wöchentlich', 'Monatlich', 'Jährlich']
                    ],
                    [
                        'name' => 'message',
                        'type' => 'textarea',
                        'required' => true
                    ]
                ],
                'status' => 'active'
            ],
            [
                'name' => 'Feedback-Formular',
                'type' => 'custom',
                'description' => 'Formular für Kundenfeedback',
                'fields' => [
                    [
                        'name' => 'rating',
                        'type' => 'select',
                        'required' => true,
                        'options' => ['1', '2', '3', '4', '5']
                    ],
                    [
                        'name' => 'experience',
                        'type' => 'textarea',
                        'required' => true
                    ],
                    [
                        'name' => 'improvement_suggestions',
                        'type' => 'textarea',
                        'required' => false
                    ],
                    [
                        'name' => 'contact_me',
                        'type' => 'checkbox',
                        'required' => false
                    ]
                ],
                'status' => 'active'
            ]
        ];

        foreach ($forms as $form) {
            Form::create($form);
        }
    }
}