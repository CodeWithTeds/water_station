@extends('layouts.customer')

@section('title', 'Order History - MW Water Refilling Station')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Order History</h1>
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if($orders->count() > 0)
        <div class="space-y-6">
            @foreach($orders as $order)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-semibold">Order #{{ $order->id }}</h3>
                            <p class="text-sm text-gray-500">{{ $order->created_at->format('F j, Y, g:i a') }}</p>
                        </div>
                        <div>
                            <span class="px-3 py-1 rounded-full text-sm font-semibold 
                                @if($order->status === 'completed') bg-green-100 text-green-800
                                @elseif($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="border-t border-b py-4 my-4">
                        <h4 class="font-medium mb-2">Items:</h4>
                        <ul class="space-y-2">
                            @foreach($order->items as $item)
                                <li class="flex justify-between">
                                    <div>
                                        <span class="font-medium">{{ $item->product->name }}</span>
                                        <span class="text-gray-600 ml-2">x {{ $item->quantity }}</span>
                                    </div>
                                    <span>₱{{ number_format($item->subtotal, 2) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm"><span class="font-medium">Delivery Address:</span> {{ $order->delivery_address }}</p>
                            @if($order->notes)
                                <p class="text-sm mt-1">
                                    <span class="font-medium">Notes:</span> 
                                    @if(strpos($order->notes, 'Loyalty discount') !== false)
                                        <span class="text-blue-600">{{ $order->notes }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block text-blue-600 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                        </svg>
                                    @else
                                        {{ $order->notes }}
                                    @endif
                                </p>
                            @endif
                        </div>
                        <div class="text-xl font-bold">₱{{ number_format($order->total_amount, 2) }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <p class="text-gray-600 text-lg">You haven't placed any orders yet.</p>
            <a href="{{ route('customer.products') }}" class="mt-4 inline-block bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-full shadow-sm transition-colors">
                Browse Products
            </a>
        </div>
    @endif
</div>
@endsection 