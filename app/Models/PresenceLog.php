<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class PresenceLog extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'presence_logs';

    protected $fillable = [
        'user_id',
        'status',
        'channel',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'status' => 'string',
        'channel' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
