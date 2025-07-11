<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Repositories\CustomerRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    protected OrderRepository $orderRepository;
    protected CustomerRepository $customerRepository;
    protected LoyaltyService $loyaltyService;

    public function __construct(
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        LoyaltyService $loyaltyService
    ) {
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Get all orders with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllOrders(int $perPage = 10): LengthAwarePaginator
    {
        return $this->orderRepository->getAllWithPagination($perPage);
    }

    /**
     * Get pending orders with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPendingOrders(int $perPage = 10): LengthAwarePaginator
    {
        return $this->orderRepository->getPendingWithPagination($perPage);
    }

    /**
     * Get an order by ID
     *
     * @param int $id
     * @return Order
     */
    public function getOrderById(int $id): Order
    {
        return $this->orderRepository->findById($id);
    }
    
    /**
     * Create a new order
     *
     * @param array $orderData
     * @param array $items
     * @param bool $useLoyaltyPoints
     * @return Order|null
     */
    public function createOrder(array $orderData, array $items, bool $useLoyaltyPoints = false): ?Order
    {
        try {
            DB::beginTransaction();
            
            $customerId = $orderData['customer_id'];
            $totalAmount = 0;
            $processedItems = [];
            $loyaltyDiscount = 0;
            
            // Calculate total and prepare items
            foreach ($items as $item) {
                if (!isset($item['product_id']) || !isset($item['quantity']) || $item['quantity'] < 1) {
                    continue; // Skip invalid items
                }
                
                $subtotal = $item['unit_price'] * $item['quantity'];
                $totalAmount += $subtotal;
                
                $processedItems[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $subtotal,
                    'refill_status' => $item['refill_status'] ?? 'new',
                ];
            }
            
            // Apply loyalty discount if requested
            if ($useLoyaltyPoints) {
                // Check if customer has enough loyalty points
                $freeProductsAvailable = $this->loyaltyService->getAvailableFreeProducts($customerId);
                
                if ($freeProductsAvailable > 0) {
                    // Apply discount
                    $discountResult = $this->loyaltyService->applyLoyaltyDiscount($processedItems);
                    $loyaltyDiscount = $discountResult['discount'];
                    $totalAmount -= $loyaltyDiscount;
                    
                    // Use loyalty points
                    $this->loyaltyService->usePoints($customerId, $this->loyaltyService->getTargetPoints());
                    
                    // Add note about loyalty discount
                    $orderData['notes'] = 'Loyalty discount applied: ₱' . number_format($loyaltyDiscount, 2);
                }
            }
            
            // Set the total amount
            $orderData['total_amount'] = $totalAmount;
            
            // Create order
            $order = $this->orderRepository->createOrder($orderData);
            
            // Create order items
            $this->orderRepository->createOrderItems($order, $processedItems);
            
            // Update product stock
            $this->orderRepository->updateProductStocks($items);
            
            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating order: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Prepare order items from request data
     *
     * @param array $requestItems
     * @return array
     */
    public function prepareOrderItems(array $requestItems): array
    {
        $items = [];
        
        foreach ($requestItems as $item) {
            if (!isset($item['product_id']) || !isset($item['quantity']) || $item['quantity'] < 1) {
                continue; // Skip invalid items
            }
            
            $product = \App\Models\Product::findOrFail($item['product_id']);
            
            $items[] = [
                'product_id' => $product->id,
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
                'refill_status' => $item['refill_status'] ?? 'new',
            ];
        }
        
        return $items;
    }

    /**
     * Approve an order
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function approveOrder(int $id, array $data): bool
    {
        try {
            DB::beginTransaction();
            
            // Update order status to processing
            $this->orderRepository->updateStatus($id, 'processing');
            
            // Update delivery date if provided
            if (isset($data['delivery_date']) && !empty($data['delivery_date'])) {
                $this->orderRepository->updateDeliveryDate($id, $data['delivery_date']);
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Complete an order
     *
     * @param int $id
     * @return bool
     */
    public function completeOrder(int $id): bool
    {
        try {
            DB::beginTransaction();
            
            // Get the order
            $order = $this->orderRepository->findById($id);
            
            // Update order status to completed
            $this->orderRepository->updateStatus($id, 'completed');
            
            // Update payment status to paid
            $this->orderRepository->updatePaymentStatus($id, 'paid');
            
            // Add loyalty points to customer (1 point per completed order)
            if ($order->customer_id) {
                $this->loyaltyService->addPoints($order->customer_id, 1);
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error completing order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cancel an order
     *
     * @param int $id
     * @param string $reason
     * @return bool
     */
    public function cancelOrder(int $id, string $reason = ''): bool
    {
        try {
            DB::beginTransaction();
            
            // Update order status to cancelled
            $this->orderRepository->updateStatus($id, 'cancelled');
            
            // Add cancellation reason to notes if provided
            if (!empty($reason)) {
                $this->orderRepository->updateNotes($id, $reason);
            }
            
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error cancelling order: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get orders by status
     *
     * @param string $status
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getOrdersByStatus(string $status, int $perPage = 10): LengthAwarePaginator
    {
        return $this->orderRepository->getByStatus($status, $perPage);
    }
    
    /**
     * Get customer's orders
     *
     * @param int $customerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCustomerOrders(int $customerId)
    {
        return $this->orderRepository->getCustomerOrders($customerId);
    }
} 