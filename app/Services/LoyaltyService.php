<?php

namespace App\Services;

use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\Log;

class LoyaltyService
{
    protected CustomerRepository $customerRepository;
    protected int $targetPoints = 10; // 10 completed orders = 1 free product
    
    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }
    
    /**
     * Get the target points required for a free product
     *
     * @return int
     */
    public function getTargetPoints(): int
    {
        return $this->targetPoints;
    }
    
    /**
     * Add loyalty points to a customer
     *
     * @param int $customerId
     * @param int $points
     * @return bool
     */
    public function addPoints(int $customerId, int $points): bool
    {
        try {
            return $this->customerRepository->addLoyaltyPoints($customerId, $points);
        } catch (\Exception $e) {
            Log::error('Error adding loyalty points: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Use loyalty points for a customer
     *
     * @param int $customerId
     * @param int $points
     * @return bool
     */
    public function usePoints(int $customerId, int $points): bool
    {
        try {
            return $this->customerRepository->useLoyaltyPoints($customerId, $points);
        } catch (\Exception $e) {
            Log::error('Error using loyalty points: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Calculate the progress percentage towards the next free product
     *
     * @param int $customerId
     * @return float
     */
    public function calculateProgressPercentage(int $customerId): float
    {
        try {
            $completedOrdersCount = $this->customerRepository->getCompletedOrdersCount($customerId);
            $pointsToNextFree = $completedOrdersCount % $this->targetPoints;
            return ($pointsToNextFree / $this->targetPoints) * 100;
        } catch (\Exception $e) {
            Log::error('Error calculating progress percentage: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get the number of free products available for a customer
     *
     * @param int $customerId
     * @return int
     */
    public function getAvailableFreeProducts(int $customerId): int
    {
        try {
            $completedOrdersCount = $this->customerRepository->getCompletedOrdersCount($customerId);
            return floor($completedOrdersCount / $this->targetPoints);
        } catch (\Exception $e) {
            Log::error('Error getting available free products: ' . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Sync loyalty points with completed orders count
     *
     * @param int $customerId
     * @return bool
     */
    public function syncPoints(int $customerId): bool
    {
        try {
            return $this->customerRepository->syncLoyaltyPoints($customerId);
        } catch (\Exception $e) {
            Log::error('Error syncing loyalty points: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Apply loyalty discount to an order
     * 
     * @param array $items
     * @return array
     */
    public function applyLoyaltyDiscount(array $items): array
    {
        // Find the cheapest product to make free
        $cheapestItem = null;
        $cheapestPrice = PHP_INT_MAX;
        
        foreach ($items as $index => $item) {
            if ($item['unit_price'] < $cheapestPrice) {
                $cheapestPrice = $item['unit_price'];
                $cheapestItem = $index;
            }
        }
        
        $discount = 0;
        if ($cheapestItem !== null) {
            $discount = $items[$cheapestItem]['unit_price'];
        }
        
        return [
            'discount' => $discount,
            'cheapestItemIndex' => $cheapestItem
        ];
    }
} 