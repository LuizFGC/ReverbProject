<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $collection = 'tickets';

    protected $fillable = [
        'titulo',
        'status',
        'prioridade',
        'user_id',
        'assigned_to',
        'categoria',
        'descricao',
        'anexos',
        'user_name'
    ];

    protected $casts = [
        'titulo' => 'string',
        'status' => 'string',
        'prioridade' => 'string',
        'user_id' => 'string',
        'user_name' => 'string',
        'assigned_to' => 'integer',
        'categoria' => 'string',
        'descricao' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'anexos' => 'array',
    ];
}
