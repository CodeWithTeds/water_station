@extends('layouts.admin')

@section('title', 'Create New Sale - MW POS System')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Create New Sale</h1>
        <a href="{{ route('admin.sales.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
            <i class="fas fa-arrow-left mr-2"></i>Back to Sales
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.sales.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Customer Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="customer_name" class="block text-gray-700 font-medium mb-2">Customer Name *</label>
                    <input type="text" id="customer_name" name="customer_name" 
                           value="{{ old('customer_name') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           required>
                </div>
                <div>
                    <label for="order_type" class="block text-gray-700 font-medium mb-2">Order Type *</label>
                    <select id="order_type" name="order_type" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="For Delivery" {{ old('order_type') == 'For Delivery' ? 'selected' : '' }}>For Delivery</option>
                        <option value="For Pickup" {{ old('order_type') == 'For Pickup' ? 'selected' : '' }}>For Pickup</option>
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <label for="delivery_address" class="block text-gray-700 font-medium mb-2">Delivery Address *</label>
                <textarea id="delivery_address" name="delivery_address" rows="3" 
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" 
                          required>{{ old('delivery_address') }}</textarea>
            </div>
        </div>

        <!-- Payment Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Payment Information</h2>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label for="payment_method" class="block text-gray-700 font-medium mb-2">Payment Method *</label>
                    <select id="payment_method" name="payment_method" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="GCash" {{ old('payment_method') == 'GCash' ? 'selected' : '' }}>GCash</option>
                        <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                    </select>
                </div>
                <div>
                    <label for="payment_status" class="block text-gray-700 font-medium mb-2">Payment Status *</label>
                    <select id="payment_status" name="payment_status" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="pending" {{ old('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ old('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Products Selection -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Select Products</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($products as $product)
                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-water text-blue-500"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-medium text-gray-800">{{ $product->name }}</h3>
                            <p class="text-green-600 font-bold">₱{{ number_format($product->price, 2) }}</p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <input type="number" 
                                   name="items[{{ $product->id }}][quantity]" 
                                   value="{{ old("items.{$product->id}.quantity", 0) }}"
                                   min="0" 
                                   class="w-16 border border-gray-300 rounded px-2 py-1 text-center"
                                   onchange="updateTotal()">
                            <input type="hidden" name="items[{{ $product->id }}][product_id]" value="{{ $product->id }}">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Summary</h2>
            <div id="orderSummary" class="space-y-3">
                <!-- Order items will be populated here -->
            </div>
            <div class="border-t pt-4 mt-4">
                <div class="flex justify-between items-center">
                    <span class="text-xl font-bold">Total Amount:</span>
                    <span class="text-xl font-bold text-green-600">₱<span id="totalAmount">0.00</span></span>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end space-x-4">
            <button type="button" onclick="clearForm()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">
                Clear Form
            </button>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded-lg font-medium">
                <i class="fas fa-save mr-2"></i>Create Sale
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    updateTotal();
    
    // Add event listeners to all quantity inputs
    document.querySelectorAll('input[name*="[quantity]"]').forEach(input => {
        input.addEventListener('change', updateTotal);
    });
});

function updateTotal() {
    let total = 0;
    const orderSummary = document.getElementById('orderSummary');
    const products = @json($products);
    
    let summaryHTML = '';
    
    products.forEach(product => {
        const quantityInput = document.querySelector(`input[name="items[${product.id}][quantity]"]`);
        const quantity = parseInt(quantityInput.value) || 0;
        
        if (quantity > 0) {
            const subtotal = quantity * product.price;
            total += subtotal;
            
            summaryHTML += `
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <div>
                        <span class="font-medium">${product.name}</span>
                        <span class="text-gray-500 ml-2">x${quantity}</span>
                    </div>
                    <span class="font-medium">₱${subtotal.toFixed(2)}</span>
                </div>
            `;
        }
    });
    
    if (summaryHTML === '') {
        summaryHTML = '<p class="text-gray-500 text-center py-4">No items selected</p>';
    }
    
    orderSummary.innerHTML = summaryHTML;
    document.getElementById('totalAmount').textContent = total.toFixed(2);
}

function clearForm() {
    if (confirm('Are you sure you want to clear the form? All entered data will be lost.')) {
        document.querySelector('form').reset();
        updateTotal();
    }
}
</script>
@endsection 