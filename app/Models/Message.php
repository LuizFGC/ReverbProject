<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Message extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'messages';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'content',
        'type',
        'anexos',
        'read_at',
        'metadata',
    ];

    protected $casts = [
        'ticket_id' => 'string',
        'user_id' => 'string',
        'content' => 'string',
        'type' => 'string',
        'anexos' => 'array',
        'metadata' => 'array',
        'read_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'sid');
    }

}
