<?php

use App\Livewire\Forms\LoginForm;
use App\Services\AuthService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

new #[Layout('layouts.auth')]
class extends Component {

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
};
?>
<div class="w-full max-w-md px-4">

    {{-- Logo --}}
    <div class="flex flex-col items-center text-center mb-8">
        <div class="w-auto rounded-2xl flex items-center justify-center mb-4">
            <img src="{{ asset('logoWhite.png') }}" class="h-18" alt="">
        </div>

        <p class="text-zinc-400 mt-1">
            {{ $form->mode === 'agent' ? 'Acesse a plataforma de gerenciamento de chamados técnicos.' : 'Consulte seus chamados, inicie chats e abra novas solicitações.' }}
        </p>
    </div>

    {{-- Card --}}
    <div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">

        {{-- Tabs --}}
        <div class="grid grid-cols-2 gap-1 bg-zinc-950 border border-zinc-800 rounded-lg p-1 mb-6">
            <button
                wire:click="switchMode('agent')"
                class="text-sm font-medium py-2 rounded-md transition-colors {{ $form->mode === 'agent' ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:text-white' }}"
            >
                Agente de Suporte
            </button>
            <button
                wire:click="switchMode('customer')"
                class="text-sm font-medium py-2 rounded-md transition-colors {{ $form->mode === 'customer' ? 'bg-indigo-600 text-white' : 'text-zinc-400 hover:text-white' }}"
            >
                Cliente / Usuário
            </button>
        </div>

        <form wire:submit="authenticate" class="space-y-5">
            <flux:input
                label="{{ $form->mode === 'agent' ? 'Endereço de E-mail corporativo' : 'Seu E-mail de Cliente' }}"
                type="email"
                icon="envelope"
                wire:model="form.email"
            />

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <flux:label>{{ $form->mode === 'agent' ? 'Senha corporativa' : 'Senha de acesso' }}</flux:label>
                </div>
                <flux:input type="password" icon="lock-closed" wire:model="form.password"/>
            </div>

            <flux:checkbox label="Manter conectado neste dispositivo" wire:model="form.remember"/>

            <flux:button type="submit" variant="primary" class="w-full justify-center">
                {{ $form->mode === 'agent' ? 'Entrar no Painel' : 'Acessar Portal do Cliente' }}
            </flux:button>
        </form>

    </div>
</div>
