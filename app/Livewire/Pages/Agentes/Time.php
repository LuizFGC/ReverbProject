<?php

namespace App\Livewire\Pages\Agentes;

use App\Services\PresenceService;
use App\Services\TicketService;
use App\Services\TimeService;
use Livewire\Attributes\Computed;
use Livewire\Component;


class Time extends Component
{


    #[Computed(cache: false)]
    public function agentes()
    {
        return app(TimeService::class)->getAgentes();
    }


    public function ticketsAtivos($id)
    {
        return app(TicketService::class)->getTicketByAgente($id);
    }


    public function isCurrentUser(string $agente): bool
    {
        return auth()->user()->sid === $agente;
    }

    public function setPresence(string $presence): void
    {
        app(PresenceService::class)->update(
            auth()->user()->sid,
            $presence
        );
        unset($this->agentes);
        $this->dispatch('$refresh');
    }

    protected function presenceMeta(string $presence): array
    {
        return match ($presence) {
            'online' => ['label' => 'Disponível', 'dot' => 'bg-emerald-500', 'badge' => 'bg-emerald-500/15 text-emerald-400'],
            'away' => ['label' => 'Ausente', 'dot' => 'bg-amber-500', 'badge' => 'bg-amber-500/15 text-amber-400'],
            default => ['label' => 'Offline', 'dot' => 'bg-zinc-500', 'badge' => 'bg-zinc-700/50 text-zinc-400'],
        };
    }
    public function render()
    {
        return view('livewire.pages.agentes.time');
    }
}
