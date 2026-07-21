<?php

namespace Database\Factories;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        $createdAt = fake()->dateTimeBetween('-7 days', 'now');

        return [
            'titulo' => fake()->sentence(4),
            'status' => fake()->randomElement([
                'Aberto',
                'Em andamento',
                'Resolvido',
            ]),
            'prioridade' => fake()->randomElement([
                'Baixa',
                'Media',
                'Alta',
                'Urgente',
            ]),
            'categoria' => fake()->randomElement([
                'TI',
                'Financeiro',
                'Suporte',
                'Infraestrutura',
            ]),
            'descricao' => fake()->paragraph(),
            'anexos' => [],
            'created_at' => $createdAt,
            'updated_at' => fake()->dateTimeBetween($createdAt, 'now'),
        ];
    }
}
