<?php

namespace App\Livewire\Pages\Auth;

use App\Livewire\Forms\LoginForm;
use App\Services\AuthService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;


 #[Layout('layouts.auth')]
class Login extends Component
{

    public LoginForm $form;

    public function switchMode(string $mode): void
    {
        $this->form->mode = $mode;
    }

    public function authenticate(AuthService $authService): void
    {
        $this->form->validate();

        if (! $authService->attempt(
            $this->form->email,
            $this->form->password,
            $this->form->remember
        )) {
            $this->form->addError(
                'email',
                'Credenciais inválidas. Verifique o e-mail e a senha.'
            );

            return;
        }

        $this->redirect(
            $authService->redirectPathForCurrentUser(),
            navigate: true
        );
    }
    public function render()
    {
        return view('livewire.pages.auth.login');
    }
}
