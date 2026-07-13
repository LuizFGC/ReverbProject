<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function rules(): array
    {
        return [

            'titulo' => 'string',

            'prioridade' => 'in:Baixa,Media,Alta,Urgente',

            'status' => 'string|in:Aberto,Em Atendimento,Resolvido,Fechado',

            'categoria' => 'string',

            'descricao' => 'string',

            'anexos' => 'array',

            'assigned_to' => 'string',
        ];
    }
}
