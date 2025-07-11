@extends('layouts.admin')

@section('title', 'Edit Sale - MW POS System')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif
    
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Sale #{{ $sale->id }}</h1>
    <form action="{{ route('admin.sales.update', $sale->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label for="customer_name" class="block text-gray-700 font-medium mb-2">Customer Name</label>
                <input type="text" id="customer_name" name="customer_name" class="w-full border rounded px-3 py-2" value="{{ old('customer_name', $sale->customer_name) }}" required>
            </div>
            <div>
                <label for="type" class="block text-gray-700 font-medium mb-2">Type</label>
                <select id="type" name="type" class="w-full border rounded px-3 py-2">
                    <option value="For Delivery" @if(old('type', $sale->order_type) == 'For Delivery') selected @endif>For Delivery</option>
                    <option value="For Pickup" @if(old('type', $sale->order_type) == 'For Pickup') selected @endif>For Pickup</option>
                </select>
            </div>
        </div>
        <div>
            <label for="delivery_address" class="block text-gray-700 font-medium mb-2">Delivery Address</label>
            <textarea id="delivery_address" name="delivery_address" rows="2" class="w-full border rounded px-3 py-2" required>{{ old('delivery_address', $sale->delivery_address) }}</textarea>
        </div>
        <hr class="my-6 border-gray-200">
        <div class="border-t pt-4 mt-6">
            <h3 class="text-lg font-semibold mb-4">Order Items</h3>
            <div class="space-y-4">
                @foreach($sale->items as $index => $item)
                <div class="grid md:grid-cols-4 gap-4 items-end border p-4 rounded">
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Product</label>
                        <select name="items[{{ $index }}][product_id]" class="w-full border rounded px-3 py-2" required>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} - ₱{{ number_format($product->price, 2) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Quantity</label>
                        <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" min="1" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Unit Price</label>
                        <input type="text" value="₱{{ number_format($item->unit_price, 2) }}" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                    </div>
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Subtotal</label>
                        <input type="text" value="₱{{ number_format($item->subtotal, 2) }}" class="w-full border rounded px-3 py-2 bg-gray-100" readonly>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        <div class="border-t pt-4 mt-6 flex justify-between items-center">
            <div class="text-xl font-bold">Total: ₱{{ number_format($sale->total_amount, 2) }}</div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-6 rounded">Update</button>
        </div>
        <div>
            <label for="payment_status" class="block text-gray-700 font-medium mb-2">Payment Status</label>
            <select id="payment_status" name="payment_status" class="w-full border rounded px-3 py-2">
                <option value="pending" @if(old('payment_status', $sale->payment_status) == 'pending') selected @endif>Pending</option>
                <option value="paid" @if(old('payment_status', $sale->payment_status) == 'paid') selected @endif>Paid</option>
                <option value="failed" @if(old('payment_status', $sale->payment_status) == 'failed') selected @endif>Failed</option>
            </select>
        </div>
    </form>
</div>
<script>
    // Simple form validation
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const itemInputs = form.querySelectorAll('input[name*="[quantity]"]');
            let hasItems = false;
            
            itemInputs.forEach(input => {
                if (parseInt(input.value) > 0) {
                    hasItems = true;
                }
            });
            
            if (!hasItems) {
                e.preventDefault();
                alert('Please ensure at least one item has a quantity greater than 0.');
                return false;
            }
        });
    });
</script>
@endsection 