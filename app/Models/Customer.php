<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fullname',
        'address',
        'contact_no',
        'password',
        'loyalty_points',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'password' => 'hashed',
        ];
    }
    
    /**
     * Add loyalty points to customer
     *
     * @param int $points
     * @return void
     */
    public function addLoyaltyPoints(int $points): void
    {
        $this->loyalty_points += $points;
        $this->save();
    }
    
    /**
     * Use loyalty points for rewards
     *
     * @param int $points
     * @return bool
     */
    public function useLoyaltyPoints(int $points): bool
    {
        if ($this->loyalty_points >= $points) {
            $this->loyalty_points -= $points;
            $this->save();
            return true;
        }
        
        return false;
    }
    
    /**
     * Get the progress percentage towards the next free product
     *
     * @param int $targetPoints
     * @return float
     */
    public function getLoyaltyProgressPercentage(int $targetPoints = 10): float
    {
        $currentPoints = $this->loyalty_points % $targetPoints;
        return ($currentPoints / $targetPoints) * 100;
    }
    
    /**
     * Get the number of free products available
     *
     * @param int $targetPoints
     * @return int
     */
    public function getAvailableFreeProducts(int $targetPoints = 10): int
    {
        return (int)($this->loyalty_points / $targetPoints);
    }
} 