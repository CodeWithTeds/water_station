@extends('layouts.admin')

@section('title', 'Admin Dashboard - MW Water Refilling Station')

@section('header', 'MW Point of sale (POS) System -Super Admin')

@section('content')
<div>
    <h1 class="text-4xl font-semibold text-gray-800 mb-6">Welcome to MW Point of sale (POS) System</h1>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Sales Today Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5 flex items-start">
                <div class="p-3 rounded-md bg-blue text-white">
                    <i class="fas fa-hand-holding-water text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-700">Total Sales Today</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">0</p>
                </div>
            </div>
        </div>
        
        <!-- Pending Orders Card -->
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="bg-white overflow-hidden shadow rounded-lg hover:bg-gray-50">
            <div class="p-5 flex items-start">
                <div class="p-3 rounded-md bg-yellow-500 text-white">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-700">Pending Orders</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ App\Models\Order::where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </a>
        
        <!-- Total Customers Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5 flex items-start">
                <div class="p-3 rounded-md bg-green-600 text-white">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-700">Total Customers</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ App\Models\Customer::count() }}</p>
                </div>
            </div>
        </div>
        
        <!-- Inventory Status Card -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5 flex items-start">
                <div class="p-3 rounded-md bg-purple-600 text-white">
                    <i class="fas fa-boxes text-2xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-700">Low Stock Items</h3>
                    <p class="mt-1 text-3xl font-semibold text-gray-900">{{ App\Models\Product::where('stock_quantity', '<', 10)->count() }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Recent Orders</h3>
                <a href="{{ route('admin.orders.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">View all</a>
            </div>
            <div class="p-4">
                @php
                    $recentOrders = App\Models\Order::with('customer')
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
                @endphp
                
                @if($recentOrders->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentOrders as $order)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-blue-600 hover:text-blue-900">#{{ $order->id }}</a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($order->isWalkInSale())
                                                <span class="text-gray-400">{{ $order->customer_name }}</span>
                                            @else
                                                {{ $order->customer_name }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            ₱{{ number_format($order->total_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($order->status === 'pending')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                            @elseif($order->status === 'processing')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Processing</span>
                                            @elseif($order->status === 'completed')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Completed</span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-500 text-center py-4">No recent orders found.</p>
                @endif
            </div>
        </div>
        
        <!-- Sales Analytics -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg font-medium leading-6 text-gray-900">Sales Analytics</h3>
            </div>
            <div class="p-4">
                <p class="text-gray-500 text-center py-4">No sales data available yet.</p>
            </div>
        </div>
    </div>
</div>
@endsection 