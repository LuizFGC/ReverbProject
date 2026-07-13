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
        'anexos',

    ];

    protected $casts = [
        'ticket_id' => 'string',
        'user_id' => 'integer',
        'content' => 'string',
        'anexos' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
