<?php

namespace App\Services;

use App\Models\Customer;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CustomerService
{
    protected CustomerRepository $customerRepository;
    protected OrderRepository $orderRepository;
    protected LoyaltyService $loyaltyService;

    public function __construct(
        CustomerRepository $customerRepository,
        OrderRepository $orderRepository,
        LoyaltyService $loyaltyService
    ) {
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->loyaltyService = $loyaltyService;
    }
    
    /**
     * Get customer by ID
     *
     * @param int $id
     * @return Customer|null
     */
    public function getCustomerById(int $id): ?Customer
    {
        try {
            return $this->customerRepository->findById($id);
        } catch (\Exception $e) {
            Log::error('Error getting customer: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create a new customer
     *
     * @param array $data
     * @return Customer|null
     */
    public function createCustomer(array $data): ?Customer
    {
        try {
            $customer = Customer::create([
                'fullname' => $data['fullname'],
                'address' => $data['address'],
                'contact_no' => $data['contact_no'],
                'password' => Hash::make($data['password']),
                'loyalty_points' => 0,
            ]);
            
            return $customer;
        } catch (\Exception $e) {
            Log::error('Error creating customer: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get customer's orders
     *
     * @param int $customerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCustomerOrders(int $customerId)
    {
        try {
            return $this->orderRepository->getCustomerOrders($customerId);
        } catch (\Exception $e) {
            Log::error('Error getting customer orders: ' . $e->getMessage());
            return collect([]);
        }
    }
    
    /**
     * Get customer's recent orders
     *
     * @param int $customerId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentOrders(int $customerId, int $limit = 3)
    {
        try {
            return $this->customerRepository->getRecentOrders($customerId, $limit);
        } catch (\Exception $e) {
            Log::error('Error getting recent orders: ' . $e->getMessage());
            return collect([]);
        }
    }
    
    /**
     * Get customer's loyalty information
     *
     * @param int $customerId
     * @return array
     */
    public function getLoyaltyInfo(int $customerId): array
    {
        try {
            $customer = $this->customerRepository->findById($customerId);
            $completedOrdersCount = $this->customerRepository->getCompletedOrdersCount($customerId);
            
            // Sync loyalty points if needed
            if ($customer->loyalty_points != $completedOrdersCount) {
                $this->loyaltyService->syncPoints($customerId);
            }
            
            $targetPoints = $this->loyaltyService->getTargetPoints();
            $pointsToNextFree = $completedOrdersCount % $targetPoints;
            $progressPercentage = $this->loyaltyService->calculateProgressPercentage($customerId);
            $freeProductsAvailable = $this->loyaltyService->getAvailableFreeProducts($customerId);
            
            return [
                'total_points' => $completedOrdersCount,
                'points_to_next_free' => $pointsToNextFree,
                'target_points' => $targetPoints,
                'progress_percentage' => $progressPercentage,
                'free_products_available' => $freeProductsAvailable
            ];
        } catch (\Exception $e) {
            Log::error('Error getting loyalty info: ' . $e->getMessage());
            return [
                'total_points' => 0,
                'points_to_next_free' => 0,
                'target_points' => $this->loyaltyService->getTargetPoints(),
                'progress_percentage' => 0,
                'free_products_available' => 0,
            ];
        }
    }
} 