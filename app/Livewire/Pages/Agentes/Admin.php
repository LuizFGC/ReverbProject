<?php

namespace App\Livewire\Pages\Agentes;

use Livewire\Component;
use App\Services\UserService;
use Livewire\Attributes\On;

class Admin extends Component
{

    public string $activeTab = 'agentes';
    public string $search = '';
    public int $totalAgentes = 0;
    public int $totalClientes = 0;

    public function mount(UserService $service)
    {
        $data = $service->countUsers();

        $this->totalAgentes = $data['agentes'];
        $this->totalClientes = $data['clientes'];
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->search = '';
    }

    #[On('usuario-salvo')]
    public function exibirMensagem(string $mensagem): void
    {
        session()->flash('status', $mensagem);
    }
    public function render()
    {
        return view('livewire.pages.agentes.admin');
    }
}
