@extends('layouts.customer')

@section('title', 'Dashboard - MW Water Refilling Station')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Welcome, {{ Auth::guard('customer')->user()->fullname }}!</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Loyalty Card -->
        <div class="bg-white rounded-lg shadow-md p-6 col-span-1">
            <h2 class="text-lg font-semibold mb-4">Loyalty Program</h2>
            
            @php
                $customer = Auth::guard('customer')->user();
                $targetPoints = 10; // 10 orders = 1 free product
                
                // Get completed orders count directly
                $completedOrdersCount = App\Models\Order::where('customer_id', $customer->id)
                    ->where('status', 'completed')
                    ->count();
                    
                $pointsToNextFree = $completedOrdersCount % $targetPoints;
                $progressPercentage = ($pointsToNextFree / $targetPoints) * 100;
                $freeProductsAvailable = floor($completedOrdersCount / $targetPoints);
            @endphp
            
            <div class="mb-4">
                <div class="flex justify-between mb-2">
                    <span class="text-sm text-gray-600">Progress to next free product</span>
                    <span class="text-sm font-medium">{{ $pointsToNextFree }} / {{ $targetPoints }} points</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2.5">
                    <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                </div>
            </div>
            
            <div class="flex justify-between items-center mb-4">
                <div>
                    <p class="text-sm text-gray-600">Total Completed Orders</p>
                    <p class="text-2xl font-bold">{{ $completedOrdersCount }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Free Products Available</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $freeProductsAvailable }}</p>
                </div>
            </div>
            
            <div class="text-xs text-gray-500">
                <p>• Earn 1 point for each completed order</p>
                <p>• Get 1 free product for every 10 points</p>
            </div>
            
            @if($freeProductsAvailable > 0)
                <div class="mt-4">
                    <a href="{{ route('customer.order') }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded text-center block">
                        Redeem Free Product
                    </a>
                </div>
            @endif
        </div>
        
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-md p-6 col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Recent Orders</h2>
                <a href="{{ route('customer.history') }}" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
            </div>
            
            @php
                $recentOrders = App\Models\Order::where('customer_id', $customer->id)
                    ->orderBy('created_at', 'desc')
                    ->take(3)
                    ->get();
            @endphp
            
            @if($recentOrders->count() > 0)
                <div class="space-y-4">
                    @foreach($recentOrders as $order)
                        <div class="border-b pb-4 last:border-b-0 last:pb-0">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="font-medium">Order #{{ $order->id }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium 
                                        @if($order->status === 'completed') bg-green-100 text-green-800
                                        @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                            <p class="text-sm mt-1">₱{{ number_format($order->total_amount, 2) }} · {{ $order->order_type }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 text-gray-500">
                    <p>No orders yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Products Display -->
    <h2 class="text-xl font-bold text-gray-800 mb-6">Our Products</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <!-- Round Gallon Container -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="h-48 flex items-center justify-center bg-gray-50 p-4">
                <img src="{{ asset('images/gallon.png') }}" alt="Round gallon container" class="h-full w-auto object-contain">
            </div>
            <div class="p-4">
                <h3 class="font-medium text-lg">Round Gallon Container</h3>
                <p class="text-gray-500 mt-1">₱35.00</p>
                <a href="{{ route('customer.order') }}" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded text-sm">
                    Order Now
                </a>
            </div>
        </div>

        <!-- Gallon Container with Faucet -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="h-48 flex items-center justify-center bg-gray-50 p-4">
                <img src="{{ asset('images/container.png') }}" alt="Gallon container with faucet" class="h-full w-auto object-contain">
            </div>
            <div class="p-4">
                <h3 class="font-medium text-lg">Gallon Container with Faucet</h3>
                <p class="text-gray-500 mt-1">₱50.00</p>
                <a href="{{ route('customer.order') }}" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded text-sm">
                    Order Now
                </a>
            </div>
        </div>
        
        <!-- Round Gallon -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="h-48 flex items-center justify-center bg-gray-50 p-4">
                <img src="{{ asset('images/Round-gallon.png') }}" alt="Round gallon" class="h-full w-auto object-contain">
            </div>
            <div class="p-4">
                <h3 class="font-medium text-lg">Round Gallon</h3>
                <p class="text-gray-500 mt-1">₱25.00</p>
                <a href="{{ route('customer.order') }}" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded text-sm">
                    Order Now
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 