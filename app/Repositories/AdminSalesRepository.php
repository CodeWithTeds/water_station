<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\Order;

class AdminSalesRepository
{
    public function getProducts()
    {
        return Product::where('is_active', true)->get();
    }

    public function createSale(array $data)
    {
        // Calculate total amount from items
        $totalAmount = 0;
        foreach ($data['items'] as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                throw new \Exception("Product with ID {$item['product_id']} not found");
            }
            $subtotal = $product->price * $item['quantity'];
            $totalAmount += $subtotal;
        }

        $order = Order::create([
            'customer_id' => null,
            'customer_name' => $data['customer_name'],
            'delivery_address' => $data['delivery_address'],
            'order_type' => $data['order_type'],
            'payment_method' => $data['payment_method'],
            'payment_status' => $data['payment_status'],
            'status' => 'completed',
            'total_amount' => $totalAmount,
        ]);

        // Create order items
        foreach ($data['items'] as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                throw new \Exception("Product with ID {$item['product_id']} not found");
            }
            $subtotal = $product->price * $item['quantity'];
            
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
                'subtotal' => $subtotal,
                'refill_status' => 'new',
            ]);

            // Update product stock
            $product->decrement('stock_quantity', $item['quantity']);
        }

        return $order;
    }

    public function getAllSales()
    {
        return \App\Models\Order::whereNull('customer_id')->orderByDesc('created_at')->get();
    }
    public function getSale($id)
    {
        return \App\Models\Order::with('items.product')->findOrFail($id);
    }
    public function updateSale($id, array $data)
    {
        $order = \App\Models\Order::findOrFail($id);
        
        // Calculate total amount from new items
        $totalAmount = 0;
        foreach ($data['items'] as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                throw new \Exception("Product with ID {$item['product_id']} not found");
            }
            $subtotal = $product->price * $item['quantity'];
            $totalAmount += $subtotal;
        }
        
        $order->update([
            'customer_name' => $data['customer_name'],
            'delivery_address' => $data['delivery_address'],
            'order_type' => $data['type'],
            'payment_status' => $data['payment_status'],
            'total_amount' => $totalAmount,
        ]);
        
        // Update items: delete old, add new
        $order->items()->delete();
        foreach ($data['items'] as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                throw new \Exception("Product with ID {$item['product_id']} not found");
            }
            $subtotal = $product->price * $item['quantity'];
            
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $product->price,
                'subtotal' => $subtotal,
                'refill_status' => 'new',
            ]);
        }
        
        return $order;
    }
    public function deleteSale($id)
    {
        $order = \App\Models\Order::findOrFail($id);
        $order->items()->delete();
        return $order->delete();
    }
} 