@extends('layouts.customer')

@section('title', 'Products - MW Water Refilling Station')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Order Products</h1>
    
    <form action="{{ route('customer.order.create') }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
        @csrf
        <div class="grid md:grid-cols-2 gap-8 mb-8">
            @foreach($products as $product)
                <div class="border rounded-lg p-4 flex flex-col">
                    <div class="flex flex-col md:flex-row items-center mb-4">
                        <div class="w-32 h-32 flex-shrink-0 mb-4 md:mb-0">
                            <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-contain">
                        </div>
                        <div class="ml-0 md:ml-4">
                            <h3 class="text-xl font-semibold">{{ $product->name }}</h3>
                            <p class="text-gray-600 mt-1">{{ $product->description }}</p>
                            <p class="text-blue-600 font-bold mt-2">₱{{ number_format($product->price, 2) }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-auto">
                        <label class="block text-gray-700 text-sm font-bold mb-2">Quantity:</label>
                        <div class="flex items-center">
                            <button type="button" onclick="decrementQuantity({{ $product->id }})" class="bg-gray-200 px-3 py-1 rounded-l text-xl font-bold">-</button>
                            <input type="number" name="products[{{ $product->id }}][quantity]" id="quantity-{{ $product->id }}" class="w-16 text-center border py-1" min="0" value="0">
                            <button type="button" onclick="incrementQuantity({{ $product->id }})" class="bg-gray-200 px-3 py-1 rounded-r text-xl font-bold">+</button>
                            <input type="hidden" name="products[{{ $product->id }}][product_id]" value="{{ $product->id }}">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mb-4">
            <label for="delivery_address" class="block text-gray-700 text-sm font-bold mb-2">Delivery Address:</label>
            <textarea name="delivery_address" id="delivery_address" rows="3" class="w-full border rounded px-3 py-2" required>{{ Auth::guard('customer')->user()->address }}</textarea>
        </div>
        
        <div class="mb-6">
            <label for="type" class="block text-gray-700 text-sm font-bold mb-2">Order Type:</label>
            <select name="type" id="type" class="w-full border rounded px-3 py-2">
                <option value="For Delivery">For Delivery</option>
                <option value="For Pickup">For Pickup</option>
            </select>
        </div>
        
        <div class="flex justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-black font-medium py-2 px-8 rounded-full shadow-sm transition-colors">
                Place Order
            </button>
        </div>
    </form>
</div>

<script>
    function incrementQuantity(productId) {
        const input = document.getElementById(`quantity-${productId}`);
        input.value = parseInt(input.value) + 1;
    }
    
    function decrementQuantity(productId) {
        const input = document.getElementById(`quantity-${productId}`);
        const currentValue = parseInt(input.value);
        if (currentValue > 0) {
            input.value = currentValue - 1;
        }
    }
</script>
@endsection 