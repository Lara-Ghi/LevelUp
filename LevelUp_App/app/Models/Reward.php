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

    // Users who favorited this reward (inverse of User->favoriteRewards)
    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'user_favorite_rewards', 'card_id', 'user_id');
    }
}