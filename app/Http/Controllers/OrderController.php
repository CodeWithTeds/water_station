<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Models\Product;
use App\Services\CustomerService;
use App\Services\OrderService;
use App\Services\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected OrderService $orderService;
    protected CustomerService $customerService;
    protected LoyaltyService $loyaltyService;
    
    public function __construct(
        OrderService $orderService,
        CustomerService $customerService,
        LoyaltyService $loyaltyService
    ) {
        $this->orderService = $orderService;
        $this->customerService = $customerService;
        $this->loyaltyService = $loyaltyService;
    }
    
    public function showProducts()
    {
        $products = Product::where('is_active', true)->get();
        return view('customer.products', compact('products'));
    }

    public function showOrderForm()
    {
        $products = Product::where('is_active', true)->get();
        return view('customer.order-form', compact('products'));
    }

    public function createOrder(CreateOrderRequest $request)
    {
        $customer = Auth::guard('customer')->user();
        
        // Check if customer has enough loyalty points if they want to use them
        if ($request->useLoyaltyPoints() && 
            $this->loyaltyService->getAvailableFreeProducts($customer->id) <= 0) {
            return redirect()->back()->with('error', 'You don\'t have enough loyalty points for a free product.');
        }

        // Prepare items and create order
        $items = $this->orderService->prepareOrderItems($request->products);
        $order = $this->orderService->createOrder(
            $request->getOrderData(), 
            $items, 
            $request->useLoyaltyPoints()
        );
        
        if (!$order) {
            return redirect()->back()->with('error', 'There was a problem creating your order. Please try again.');
        }

        return redirect()->route('customer.history')
            ->with('success', 'Order placed successfully! Your order number is #' . $order->id);
    }

    public function history()
    {
        $customer = Auth::guard('customer')->user();
        $orders = $this->customerService->getCustomerOrders($customer->id);
        return view('customer.history', compact('orders'));
    }
} 