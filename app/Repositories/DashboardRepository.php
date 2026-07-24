<?php

namespace App\Repositories;

use App\Models\Ticket;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection ;

class DashboardRepository
{
    public function getUrgentes($prioridade, $filtros): Collection
    {
        return Ticket::where('prioridade', $prioridade)
            ->whereIn('status', ['Aberto', 'Em andamento'])
            ->when(
                ! empty($filtros['agente']),
                fn ($query) => $query->where('assigned_to', $filtros['agente'])
            )
            ->get();
    }

    public function getByStatus($status, $filtros): int
    {
        return Ticket::where('status', $status)
            ->when(
                ! empty($filtros['agente']),
                fn ($query) => $query->where('assigned_to', $filtros['agente'])
            )
            ->count();
    }

    public function getVolumeSemanal($filtros): array
    {
        $inicio = now()->subDays(6)->startOfDay();
        $fim = now()->endOfDay();

        $abertosPorDia = $this->contarPorDia(
            $this->buscarTicketsPorPeriodo('created_at', $inicio, $fim, $filtros),
            'created_at'
        );

        $resolvidosPorDia = $this->contarPorDia(
            $this->buscarTicketsPorPeriodo('updated_at', $inicio, $fim, $filtros, 'Resolvido'),
            'updated_at'
        );

        return $this->montarSerieDeDias($inicio, $abertosPorDia, $resolvidosPorDia);
    }

    private function buscarTicketsPorPeriodo(
        string $campoData,
        CarbonInterface $inicio,
        CarbonInterface $fim,
        array $filtros,
        ?string $status = null
    ): Collection {
        return Ticket::whereBetween($campoData, [$inicio, $fim])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when(
                ! empty($filtros['agente']),
                fn ($query) => $query->where('assigned_to', $filtros['agente'])
            )
            ->get([$campoData]);
    }

    private function contarPorDia(Collection $tickets, string $campoData): Collection
    {
        return $tickets
            ->groupBy(fn ($ticket) => $ticket->{$campoData}->format('Y-m-d'))
            ->map->count();
    }

    private function montarSerieDeDias(
        CarbonInterface $inicio,
        Collection $abertosPorDia,
        Collection $resolvidosPorDia
    ): array {
        return collect(range(0, 6))->map(function ($i) use ($inicio, $abertosPorDia, $resolvidosPorDia) {
            $data = $inicio->addDays($i);
            $chave = $data->format('Y-m-d');

            return [
                'dia' => $this->diaAbreviado($data),
                'abertos' => $abertosPorDia[$chave] ?? 0,
                'resolvidos' => $resolvidosPorDia[$chave] ?? 0,
            ];
        })->toArray();
    }

    private function diaAbreviado(CarbonInterface $data): string
    {
        return match ($data->dayOfWeekIso) {
            1 => 'Seg',
            2 => 'Ter',
            3 => 'Qua',
            4 => 'Qui',
            5 => 'Sex',
            6 => 'Sáb',
            7 => 'Dom',
        };
    }

    public function getRecentes($filtros): Collection
    {
        return Ticket::when(
            ! empty($filtros['agente']),
            fn ($query) => $query->where('assigned_to', $filtros['agente'])
        )
            ->latest()
            ->limit(5)
            ->get();
    }
}
