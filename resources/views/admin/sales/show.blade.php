@extends('layouts.admin')

@section('title', 'Sale Details - MW POS System')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Sale #{{ $sale->id }}</h1>
        <a href="{{ route('admin.sales.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium py-2 px-4 rounded">Back to Sales</a>
    </div>
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="mb-2"><span class="font-semibold">Customer:</span> {{ $sale->customer_name }}</div>
        <div class="mb-2"><span class="font-semibold">Date:</span> {{ $sale->created_at->format('M d, Y h:i A') }}</div>
        <div class="mb-2"><span class="font-semibold">Delivery Address:</span> {{ $sale->delivery_address }}</div>
        <div class="mb-2"><span class="font-semibold">Type:</span> {{ $sale->order_type }}</div>
        <div class="mb-2"><span class="font-semibold">Payment Status:</span> {{ $sale->payment_status }}</div>
        <div class="mb-2"><span class="font-semibold">Total:</span> ₱{{ number_format($sale->total_amount, 2) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Items</h2>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subtotal</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($sale->items as $item)
                    <tr>
                        <td class="px-4 py-2">{{ $item->product->name ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $item->quantity }}</td>
                        <td class="px-4 py-2">₱{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-4 py-2">₱{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection 