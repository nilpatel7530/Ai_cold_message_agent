<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'name',
        'key',
        'port',
        'startup_command',
        'shutdown_command',
        'pid',
        'description',
    ];
}
