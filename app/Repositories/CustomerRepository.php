<?php

namespace App\Repositories;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomerRepository
{
    /**
     * Find a customer by ID
     *
     * @param int $id
     * @return Customer|null
     */
    public function findById(int $id): ?Customer
    {
        return Customer::findOrFail($id);
    }
    
    /**
     * Update customer loyalty points
     *
     * @param int $customerId
     * @param int $points
     * @return bool
     */
    public function updateLoyaltyPoints(int $customerId, int $points): bool
    {
        $customer = $this->findById($customerId);
        $customer->loyalty_points = $points;
        return $customer->save();
    }
    
    /**
     * Add loyalty points to customer
     *
     * @param int $customerId
     * @param int $points
     * @return bool
     */
    public function addLoyaltyPoints(int $customerId, int $points): bool
    {
        $customer = $this->findById($customerId);
        $customer->loyalty_points += $points;
        return $customer->save();
    }
    
    /**
     * Use loyalty points (subtract from customer)
     *
     * @param int $customerId
     * @param int $points
     * @return bool
     */
    public function useLoyaltyPoints(int $customerId, int $points): bool
    {
        $customer = $this->findById($customerId);
        if ($customer->loyalty_points >= $points) {
            $customer->loyalty_points -= $points;
            return $customer->save();
        }
        return false;
    }
    
    /**
     * Get customer's completed orders count
     *
     * @param int $customerId
     * @return int
     */
    public function getCompletedOrdersCount(int $customerId): int
    {
        return Order::where('customer_id', $customerId)
            ->where('status', 'completed')
            ->count();
    }
    
    /**
     * Get customer's recent orders
     *
     * @param int $customerId
     * @param int $limit
     * @return Collection
     */
    public function getRecentOrders(int $customerId, int $limit = 3): Collection
    {
        return Order::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->take($limit)
            ->get();
    }
    
    /**
     * Get customer's orders with pagination
     *
     * @param int $customerId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getOrdersWithPagination(int $customerId, int $perPage = 10): LengthAwarePaginator
    {
        return Order::where('customer_id', $customerId)
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    /**
     * Sync loyalty points with completed orders count
     *
     * @param int $customerId
     * @return bool
     */
    public function syncLoyaltyPoints(int $customerId): bool
    {
        $customer = $this->findById($customerId);
        $completedOrdersCount = $this->getCompletedOrdersCount($customerId);
        
        if ($customer->loyalty_points != $completedOrdersCount) {
            $customer->loyalty_points = $completedOrdersCount;
            return $customer->save();
        }
        
        return true;
    }
} 