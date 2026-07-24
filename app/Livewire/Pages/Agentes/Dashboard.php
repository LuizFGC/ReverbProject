<?php

namespace App\Livewire\Pages\Agentes;

use App\Services\DashboardService;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Dashboard extends Component
{
    #[Computed]
    public function dashboardData()
    {
        $filters = [];

        if (auth()->user()->role !== 'gerente') {
            $filters['agente'] = auth()->user()->sid;
        }

        return app(DashboardService::class)->getDashboardData($filters);
    }

    #[Computed]
    public function stats(): array
    {
        return [
            [
                'label' => 'EM ABERTO',
                'value' => $this->dashboardData['totalAberto'],
                'icon' => 'inbox',
                'bg-color' => 'bg-blue-900',
                'color' => 'text-blue-500',
            ],
            [
                'label' => 'EM ATENDIMENTO',
                'value' => $this->dashboardData['emAtendimento'],
                'icon' => 'clock',
                'bg-color' => 'bg-yellow-900',
                'color' => 'text-yellow-500',
            ],
            [
                'label' => 'RESOLVIDOS',
                'value' => $this->dashboardData['fechado'],
                'icon' => 'check-circle',
                'bg-color' => 'bg-green-900',
                'color' => 'text-green-500',
            ],
            [
                'label' => 'TEMPO MÉDIO DE RESPOSTA',
                'value' => '14m', 'icon' => 'user-plus',
                'bg-color' => 'bg-indigo-950',
                'color' => ' text-indigo-500'],

        ];
    }

    public function openTicket(string $id): void
    {
        $this->redirect("tickets/$id");
    }

    public array $recentActivity = [

    ];

    public function render()
    {
        return view('livewire.pages.agentes.dashboard');
    }
}
