<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'total_points',
        'daily_points',
        'last_points_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_points_date' => 'date',
        ];
    }

    /**
     * Relationship with HealthCycles
     */
    public function healthCycles()
    {
        return $this->hasMany(HealthCycle::class);
    }

    /**
     * Reset daily points if it's a new day
     */
    public function resetDailyPointsIfNeeded()
    {
        $today = now()->toDateString();
        
        if ($this->last_points_date !== $today) {
            $this->daily_points = 0;
            $this->last_points_date = $today;
            $this->save();
        }
    }

    /**
     * Check if user can earn more points today
     */
    public function canEarnPoints()
    {
        $this->resetDailyPointsIfNeeded();
        return $this->daily_points < 100;
    }

    /**
     * Add points to user (respecting daily limit)
     */
    public function addPoints($points)
    {
        $this->resetDailyPointsIfNeeded();
        
        if ($this->daily_points >= 100) {
            return 0; // Already at daily limit
        }
        
        $pointsToAdd = min($points, 100 - $this->daily_points);
        
        $this->daily_points += $pointsToAdd;
        $this->total_points += $pointsToAdd;
        $this->save();
        
        return $pointsToAdd;
    }
}
