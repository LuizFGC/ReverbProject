<?php

namespace App\Services;

use App\Repositories\DashboardRepository;


class DashboardService
{
    private DashboardRepository $repository;

    public function __construct(DashboardRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getDashboardData($filtros)
    {

        return [
            'totalAberto' => $this->getByStatus('Aberto', $filtros),
            'fechado' => $this->getByStatus('Aberto', $filtros),
            'urgentes' => $this->getByPrioridade('urgente', $filtros),
            'emAtendimento' => $this->getByStatus('Aberto', $filtros),
        ];
    }
}
