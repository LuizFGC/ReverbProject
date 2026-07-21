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
            'totalAberto' => $this->repository->getByStatus('Aberto', $filtros),
            'fechado' => $this->repository->getByStatus('Resolvido', $filtros),
            'urgentes' => $this->repository->getUrgentes('Urgente', $filtros),
            'emAtendimento' => $this->repository->getByStatus('Em andamento', $filtros),
            'volumeSemanal' => $this->repository->getVolumeSemanal($filtros),
        ];
    }
}
