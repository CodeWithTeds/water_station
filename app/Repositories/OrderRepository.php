<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository
{
    /**
     * Get all orders with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllWithPagination(int $perPage = 10): LengthAwarePaginator
    {
        return Order::with(['customer', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get pending orders with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPendingWithPagination(int $perPage = 10): LengthAwarePaginator
    {
        return Order::with(['customer', 'items.product'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get an order by ID with relationships
     *
     * @param int $id
     * @return Order|null
     */
    public function findById(int $id): ?Order
    {
        return Order::with(['customer', 'items.product'])
            ->findOrFail($id);
    }

    /**
     * Create a new order
     *
     * @param array $orderData
     * @return Order
     */
    public function createOrder(array $orderData): Order
    {
        return Order::create($orderData);
    }
    
    /**
     * Create order items for an order
     *
     * @param Order $order
     * @param array $items
     * @return Collection
     */
    public function createOrderItems(Order $order, array $items): Collection
    {
        $orderItems = [];
        foreach ($items as $item) {
            $orderItems[] = $order->items()->create($item);
        }
        
        return collect($orderItems);
    }
    
    /**
     * Update product stock quantities
     *
     * @param array $items
     * @return void
     */
    public function updateProductStocks(array $items): void
    {
        foreach ($items as $item) {
            if (isset($item['product_id']) && isset($item['quantity'])) {
                $product = Product::findOrFail($item['product_id']);
                $product->stock_quantity = $product->stock_quantity - $item['quantity'];
                $product->save();
            }
        }
    }

    /**
     * Update order status
     *
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool
    {
        $order = Order::findOrFail($id);
        $order->status = $status;
        return $order->save();
    }

    /**
     * Update payment status
     *
     * @param int $id
     * @param string $paymentStatus
     * @return bool
     */
    public function updatePaymentStatus(int $id, string $paymentStatus): bool
    {
        $order = Order::findOrFail($id);
        $order->payment_status = $paymentStatus;
        return $order->save();
    }

    /**
     * Update delivery date
     *
     * @param int $id
     * @param string $deliveryDate
     * @return bool
     */
    public function updateDeliveryDate(int $id, string $deliveryDate): bool
    {
        $order = Order::findOrFail($id);
        $order->delivery_date = $deliveryDate;
        return $order->save();
    }
    
    /**
     * Update order notes
     *
     * @param int $id
     * @param string $notes
     * @return bool
     */
    public function updateNotes(int $id, string $notes): bool
    {
        $order = Order::findOrFail($id);
        $order->notes = $notes;
        return $order->save();
    }

    /**
     * Get orders by status
     *
     * @param string $status
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByStatus(string $status, int $perPage = 10): LengthAwarePaginator
    {
        return Order::with(['customer', 'items.product'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }
    
    /**
     * Get customer's orders
     *
     * @param int $customerId
     * @return Collection
     */
    public function getCustomerOrders(int $customerId): Collection
    {
        return Order::where('customer_id', $customerId)
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->get();
    }
} 