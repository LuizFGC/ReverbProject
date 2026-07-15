<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function rules(): array
    {
        return [

            'prioridade' => 'in:Baixa,Media,Alta,Urgente',

            'status' => 'string|in:Aberto,Em Atendimento,Resolvido,Fechado',

            'categoria' => 'string',

            'assigned_to' => 'string',
        ];
    }
}
