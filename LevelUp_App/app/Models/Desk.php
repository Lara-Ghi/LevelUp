<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desk extends Model
{
    protected $table = 'desks';
    
    protected $fillable = [
        'desk_model',
        'serial_number',
    ];
}
