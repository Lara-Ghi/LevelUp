<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Desk extends Model
{
    protected $table = 'desks';

    protected $fillable = [
        'name',
        'desk_model',
        'serial_number',
    ];

    // user-desk relationship
    public function user()
    {
        return $this->hasOne(User::class, 'desk_id', 'id');
    }
}
