<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['string', 'max:255'],
            'email' => ['email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'current_password' => ['nullable', 'string', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.string' => 'Der Name muss ein Text sein.',
            'name.max' => 'Der Name darf maximal 255 Zeichen haben.',
            'email.email' => 'Die E-Mail-Adresse muss gültig sein.',
            'email.max' => 'Die E-Mail-Adresse darf maximal 255 Zeichen haben.',
            'email.unique' => 'Diese E-Mail-Adresse wird bereits verwendet.',
            'current_password.current_password' => 'Das aktuelle Passwort ist nicht korrekt.',
            'password.min' => 'Das neue Passwort muss mindestens 8 Zeichen haben.',
            'password.confirmed' => 'Die Passwort-Bestätigung stimmt nicht überein.',
        ];
    }
}
