<?php

namespace App\Services;

use App\Repositories\TimeRespository;

class TimeService
{
    public function __construct(
        private TimeRespository $timeRepository
    ) {}

    public function getAgentes()
    {
        $data = [];
        $agentes = $this->timeRepository->getAll();

        foreach ($agentes as $agente) {

            $data[] = [
                'sid' => $agente->sid,
                'name' => $agente->name,
                'cargo' => $agente->cargo,
                'departamento' => $agente->departamento,
                'email' => $agente->email,

            ];
        }

        return $data;
    }
}
