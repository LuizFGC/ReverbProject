<?php

namespace App\Services;

use App\Repositories\PresenceRepository;
use App\Repositories\TicketRepository;
use App\Repositories\TimeRespository;

class TimeService
{
    public function __construct(
        private TimeRespository $timeRepository,
        private TicketRepository $ticketRepository,
        private PresenceRepository $presenceRepository
    ) {}

    public function getAgentes()
    {
        $agentes = $this->timeRepository->getAll();

        $agentIds = $agentes->pluck('sid');

        $presences = $this->getPresenceLogs($agentIds);

        $tickets = $this->getTickets($agentIds);

        return $agentes->map(function ($agent) use ($presences, $tickets) {

            $lastPresence = $presences->get($agent->user_id)?->first();

            return [
                'user' =>[
                    'name' => $agent->name,
                    'email' => $agent->email,
                    'cargo' => $agent->cargo,
                ],

                'isCurrentUser' => $agent->sid === auth()->user()->sid,

                'presence' => $lastPresence?->status ?? 'offline',

                'activeTickets' => $tickets->get($agent->user_id)?->count() ?? 0,
            ];
        });
    }

    private function getPresenceLogs($agentIds)
    {
        return $this->presenceRepository->getPresenceLogs($agentIds);
    }

    private function getTickets($agentIds)
    {
        return $this->ticketRepository->getByAgent($agentIds);
    }
}
