<?php

namespace App\Http\Requests\Tickets;

use Illuminate\Foundation\Http\FormRequest;

class CreateTicketRequest extends FormRequest
{
    public function rules(): array
    {
        return [


            'titulo' => 'required|string',

            'prioridade' => 'required|in:Baixa,Media,Alta,Urgente',

            'categoria' => 'required|string',

            'descricao' => 'required|string',

            'anexos' => 'array',

        ];
    }



}
