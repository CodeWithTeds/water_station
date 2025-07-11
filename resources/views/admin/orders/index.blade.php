@extends('layouts.admin')

@section('title', 'Order Management - MW Water Refilling Station')

@section('content')
<div class="container px-6 py-8 mx-auto">
    <h1 class="text-2xl font-semibold text-gray-800">Order Management</h1>
    
    <div class="mt-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif
    </div>

    <!-- Order Status Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center">
            <li class="mr-2">
                <a href="{{ route('admin.orders.index') }}" class="inline-block p-4 rounded-t-lg {{ $activeTab === 'all' ? 'border-b-2 border-blue-600 text-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                    All Orders
                </a>
            </li>
            <li class="mr-2">
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="inline-block p-4 rounded-t-lg {{ $activeTab === 'pending' ? 'border-b-2 border-blue-600 text-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                    Pending
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full ml-2">
                        {{ App\Models\Order::where('status', 'pending')->count() }}
                    </span>
                </a>
            </li>
            <li class="mr-2">
                <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" class="inline-block p-4 rounded-t-lg {{ $activeTab === 'processing' ? 'border-b-2 border-blue-600 text-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                    Processing
                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full ml-2">
                        {{ App\Models\Order::where('status', 'processing')->count() }}
                    </span>
                </a>
            </li>
            <li class="mr-2">
                <a href="{{ route('admin.orders.index', ['status' => 'completed']) }}" class="inline-block p-4 rounded-t-lg {{ $activeTab === 'completed' ? 'border-b-2 border-blue-600 text-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                    Completed
                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full ml-2">
                        {{ App\Models\Order::where('status', 'completed')->count() }}
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="inline-block p-4 rounded-t-lg {{ $activeTab === 'cancelled' ? 'border-b-2 border-blue-600 text-blue-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                    Cancelled
                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full ml-2">
                        {{ App\Models\Order::where('status', 'cancelled')->count() }}
                    </span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Orders Table -->
    <div class="overflow-x-auto relative shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="py-3 px-6">Order ID</th>
                    <th scope="col" class="py-3 px-6">Customer</th>
                    <th scope="col" class="py-3 px-6">Date</th>
                    <th scope="col" class="py-3 px-6">Total</th>
                    <th scope="col" class="py-3 px-6">Type</th>
                    <th scope="col" class="py-3 px-6">Status</th>
                    <th scope="col" class="py-3 px-6">Payment</th>
                    <th scope="col" class="py-3 px-6">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="py-4 px-6 font-medium text-gray-900">#{{ $order->id }}</td>
                        <td class="py-4 px-6">
                            @if($order->isWalkInSale())
                                <span class="text-gray-400">{{ $order->customer_name }}</span>
                            @else
                                {{ $order->customer_name }}
                            @endif
                        </td>
                        <td class="py-4 px-6">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                        <td class="py-4 px-6">₱{{ number_format($order->total_amount, 2) }}</td>
                        <td class="py-4 px-6">{{ $order->order_type }}</td>
                        <td class="py-4 px-6">
                            @if($order->status === 'pending')
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Pending</span>
                            @elseif($order->status === 'processing')
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Processing</span>
                            @elseif($order->status === 'completed')
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Completed</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Cancelled</span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            @if($order->payment_status === 'pending')
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Pending</span>
                            @elseif($order->payment_status === 'paid')
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Paid</span>
                            @else
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Failed</span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="font-medium text-blue-600 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white border-b">
                        <td colspan="8" class="py-4 px-6 text-center">No orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $orders->links() }}
    </div>
</div>
@endsection 