<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;
use MongoDB\Laravel\Eloquent\HybridRelations;

class Ticket extends Model
{
    use HasFactory, HybridRelations;

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
        'user_name',
    ];

    protected $casts = [
        'titulo' => 'string',
        'status' => 'string',
        'prioridade' => 'string',
        'user_id' => 'string',
        'user_name' => 'string',
        'assigned_to' => 'string',
        'categoria' => 'string',
        'descricao' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'anexos' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id', 'sid');
    }


    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'sid');

    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'ticket_id')
            ->orderBy('created_at', 'asc');
    }
}
