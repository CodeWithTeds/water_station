@extends('layouts.admin')

@section('title', 'Order Details - MW Water Refilling Station')

@section('content')
<div class="container px-6 py-8 mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">Order #{{ $order->id }}</h1>
            <p class="text-sm text-gray-600">Placed on {{ $order->created_at->format('F j, Y, g:i a') }}</p>
        </div>
        <a href="{{ route('admin.orders.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded">
            Back to Orders
        </a>
    </div>

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

    <!-- Order Status -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex flex-wrap justify-between items-center">
            <div class="mb-4 md:mb-0">
                <h2 class="text-lg font-semibold">Status</h2>
                <div class="mt-2">
                    @if($order->status === 'pending')
                        <span class="bg-yellow-100 text-yellow-800 text-sm font-medium px-3 py-1 rounded-full">Pending</span>
                    @elseif($order->status === 'processing')
                        <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">Processing</span>
                    @elseif($order->status === 'completed')
                        <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">Completed</span>
                    @else
                        <span class="bg-red-100 text-red-800 text-sm font-medium px-3 py-1 rounded-full">Cancelled</span>
                    @endif
                </div>
            </div>
            
            <div class="mb-4 md:mb-0">
                <h2 class="text-lg font-semibold">Payment</h2>
                <div class="mt-2">
                    <span class="font-medium">Method:</span> {{ $order->payment_method }}<br>
                    <span class="font-medium">Status:</span> 
                    @if($order->payment_status === 'pending')
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2 py-0.5 rounded-full">Pending</span>
                    @elseif($order->payment_status === 'paid')
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2 py-0.5 rounded-full">Paid</span>
                    @else
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-0.5 rounded-full">Failed</span>
                    @endif
                </div>
            </div>
            
            <div class="mb-4 md:mb-0">
                <h2 class="text-lg font-semibold">Delivery</h2>
                <div class="mt-2">
                    <span class="font-medium">Type:</span> {{ $order->order_type }}<br>
                    @if($order->delivery_date)
                        <span class="font-medium">Date:</span> {{ \Carbon\Carbon::parse($order->delivery_date)->format('M d, Y') }}
                    @else
                        <span class="font-medium">Date:</span> Not scheduled
                    @endif
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="w-full md:w-auto mt-4 md:mt-0">
                @if($order->status === 'pending')
                    <button onclick="document.getElementById('approve-modal').classList.remove('hidden')" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded mr-2">
                        Approve Order
                    </button>
                    <button onclick="document.getElementById('cancel-modal').classList.remove('hidden')" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded">
                        Cancel Order
                    </button>
                @elseif($order->status === 'processing')
                    <form action="{{ route('admin.orders.complete', $order->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded">
                            Mark as Completed
                        </button>
                    </form>
                    <button onclick="document.getElementById('cancel-modal').classList.remove('hidden')" class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded ml-2">
                        Cancel Order
                    </button>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Customer Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">Customer Information</h2>
            @if($order->isWalkInSale())
                <p><span class="font-medium">Name:</span> <span class="text-gray-400">{{ $order->customer_name }}</span></p>
                <p><span class="font-medium">Type:</span> <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded-full">Walk-in Sale</span></p>
                <p><span class="font-medium">Address:</span> {{ $order->delivery_address }}</p>
            @else
                <p><span class="font-medium">Name:</span> {{ $order->customer->fullname }}</p>
                <p><span class="font-medium">Contact:</span> {{ $order->customer->contact_no }}</p>
                <p><span class="font-medium">Email:</span> {{ $order->customer->email }}</p>
                <p><span class="font-medium">Address:</span> {{ $order->delivery_address }}</p>
            @endif
        </div>

        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow-md p-6 md:col-span-2">
            <h2 class="text-lg font-semibold mb-4">Order Summary</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="py-3 px-6">Product</th>
                            <th scope="col" class="py-3 px-6">Refill Status</th>
                            <th scope="col" class="py-3 px-6">Price</th>
                            <th scope="col" class="py-3 px-6">Quantity</th>
                            <th scope="col" class="py-3 px-6">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr class="bg-white border-b">
                                <td class="py-4 px-6 font-medium text-gray-900">{{ $item->product->name }}</td>
                                <td class="py-4 px-6">
                                    @if($item->refill_status === 'new')
                                        <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">New</span>
                                    @elseif($item->refill_status === 'refill')
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Refill</span>
                                    @else
                                        <span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Replace</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">₱{{ number_format($item->unit_price, 2) }}</td>
                                <td class="py-4 px-6">{{ $item->quantity }}</td>
                                <td class="py-4 px-6">₱{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="font-semibold text-gray-900">
                            <td class="py-4 px-6 text-base" colspan="4">Total</td>
                            <td class="py-4 px-6 text-base">₱{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    @if($order->notes)
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-lg font-semibold mb-2">Notes</h2>
            <p class="text-gray-700">{{ $order->notes }}</p>
        </div>
    @endif
</div>

<!-- Approve Order Modal -->
<div id="approve-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Approve Order</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    You are about to approve this order. This will change the status to "Processing".
                </p>
                <form action="{{ route('admin.orders.approve', $order->id) }}" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-4">
                        <label for="delivery_date" class="block text-sm font-medium text-gray-700">Delivery Date</label>
                        <input type="date" id="delivery_date" name="delivery_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    </div>
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700">Additional Notes</label>
                        <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"></textarea>
                    </div>
                    <div class="flex justify-between mt-6">
                        <button type="button" onclick="document.getElementById('approve-modal').classList.add('hidden')" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                            Cancel
                        </button>
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Approve Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Order Modal -->
<div id="cancel-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Cancel Order</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to cancel this order? This action cannot be undone.
                </p>
                <form action="{{ route('admin.orders.cancel', $order->id) }}" method="POST" class="mt-4">
                    @csrf
                    <div class="mb-4">
                        <label for="reason" class="block text-sm font-medium text-gray-700">Reason for Cancellation</label>
                        <textarea id="reason" name="reason" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-500 focus:ring-opacity-50" required></textarea>
                    </div>
                    <div class="flex justify-between mt-6">
                        <button type="button" onclick="document.getElementById('cancel-modal').classList.add('hidden')" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-2 px-4 rounded">
                            Back
                        </button>
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            Cancel Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 