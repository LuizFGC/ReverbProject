<?php

namespace App\Livewire\Forms;

use App\Services\AuthService;
use Livewire\Form;

class LoginForm extends Form
{
    public string $mode = 'agent';
    public string $email = '';
    public string $password = '';
    public bool $remember = true;

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ];
    }
}
