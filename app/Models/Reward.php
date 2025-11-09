<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    protected $table = 'rewards_catalog';
    
    protected $fillable = [
        'card_name',
        'points_amount',
        'card_description',
        'card_image',
    ];
}