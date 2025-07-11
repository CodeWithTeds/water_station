@extends('layouts.customer')

@section('title', 'Create Order - MW Water Refilling Station')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Create New Sale</h1>
    
    <form id="orderForm" action="{{ route('customer.order.create') }}" method="POST" class="space-y-6">
        @csrf
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <label for="customer_name" class="block text-gray-700 font-medium mb-2">Customer Name</label>
                <input type="text" id="customer_name" name="customer_name" value="{{ Auth::guard('customer')->user()->fullname }}" class="w-full border rounded px-3 py-2" required>
            </div>
            
            <div>
                <label for="type" class="block text-gray-700 font-medium mb-2">Type</label>
                <select id="type" name="type" class="w-full border rounded px-3 py-2">
                    <option value="For Delivery">For Delivery</option>
                    <option value="For Pickup">For Pickup</option>
                </select>
            </div>
        </div>
        
        <div>
            <label for="delivery_address" class="block text-gray-700 font-medium mb-2">Delivery Address</label>
            <textarea id="delivery_address" name="delivery_address" rows="3" class="w-full border rounded px-3 py-2" required>{{ Auth::guard('customer')->user()->address }}</textarea>
        </div>
        
        <hr class="my-6 border-gray-200">
        
        <div id="orderItems">
            <!-- Order items will be added here -->
            <div class="order-item grid md:grid-cols-3 gap-4 items-end">
                <div>
                    <label for="product_id" class="block text-gray-700 font-medium mb-2">Jar Type</label>
                    <select id="product_id" name="products[0][product_id]" class="product-select w-full border rounded px-3 py-2">
                        @foreach(\App\Models\Product::all() as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} - ₱{{ number_format($product->price, 2) }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="quantity" class="block text-gray-700 font-medium mb-2">Quantity</label>
                    <input type="number" id="quantity" name="products[0][quantity]" value="1" min="1" class="quantity-input w-full border rounded px-3 py-2" required>
                </div>
                
                <div>
                    <button type="button" id="addItem" class="bg-blue-500 hover:bg-blue-600 text-black font-medium py-2 px-4 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add
                    </button>
                </div>
            </div>
        </div>
        
        <div id="orderItemsList" class="mt-4">
            <!-- Added items will display here -->
        </div>
        
        @php
            $customer = Auth::guard('customer')->user();
            $targetPoints = 10; // 10 orders = 1 free product
            
            // Get completed orders count directly
            $completedOrdersCount = App\Models\Order::where('customer_id', $customer->id)
                ->where('status', 'completed')
                ->count();
                
            // Calculate free products available
            $freeProductsAvailable = floor($completedOrdersCount / $targetPoints);
        @endphp
        
        @if($freeProductsAvailable > 0)
            <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center">
                    <input type="checkbox" id="use_loyalty_points" name="use_loyalty_points" value="1" class="h-4 w-4 text-blue-600">
                    <label for="use_loyalty_points" class="ml-2 block text-sm text-gray-700">
                        Use loyalty points for a free product (You have {{ $freeProductsAvailable }} free product(s) available)
                    </label>
                </div>
                <p class="mt-1 text-xs text-gray-500">Get your cheapest product for free by redeeming 10 loyalty points.</p>
            </div>
        @endif
        
        <div class="border-t pt-4 mt-6 flex justify-between items-center">
            <div class="text-xl font-bold">Total: ₱<span id="orderTotal">0.00</span></div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-black font-medium py-2 px-6 rounded">
                Place Order
            </button>
        </div>
    </form>
</div>

<script>
    let itemCount = 1;
    let items = [];
    const products = @json(\App\Models\Product::all());
    
    document.addEventListener('DOMContentLoaded', function() {
        // Add item button
        document.getElementById('addItem').addEventListener('click', function() {
            const productSelect = document.querySelector('.product-select');
            const quantityInput = document.querySelector('.quantity-input');
            
            const productId = productSelect.value;
            const quantity = parseInt(quantityInput.value);
            
            if (quantity < 1) {
                alert('Quantity must be at least 1');
                return;
            }
            
            const product = products.find(p => p.id == productId);
            
            // Add to items array
            items.push({
                productId: productId,
                name: product.name,
                price: product.price,
                quantity: quantity,
                subtotal: product.price * quantity
            });
            
            // Reset quantity
            quantityInput.value = 1;
            
            // Update display
            updateOrderItemsList();
            updateTotal();
            
            // Add hidden fields for form submission
            const form = document.getElementById('orderForm');
            
            // Clear existing hidden inputs
            const existingInputs = form.querySelectorAll('input[name^="products["][name$="][product_id]"]');
            existingInputs.forEach(input => {
                if (input.id.startsWith('hidden_')) {
                    input.remove();
                }
            });
            
            const quantityInputs = form.querySelectorAll('input[name^="products["][name$="][quantity]"]');
            quantityInputs.forEach(input => {
                if (input.id.startsWith('hidden_')) {
                    input.remove();
                }
            });
            
            // Add new hidden inputs
            items.forEach((item, index) => {
                if (index === 0) return; // Skip first one as it's in the visible form
                
                const productIdInput = document.createElement('input');
                productIdInput.type = 'hidden';
                productIdInput.id = 'hidden_product_' + index;
                productIdInput.name = 'products[' + index + '][product_id]';
                productIdInput.value = item.productId;
                form.appendChild(productIdInput);
                
                const quantityInput = document.createElement('input');
                quantityInput.type = 'hidden';
                quantityInput.id = 'hidden_quantity_' + index;
                quantityInput.name = 'products[' + index + '][quantity]';
                quantityInput.value = item.quantity;
                form.appendChild(quantityInput);
            });
            
        });
        
        // Loyalty points checkbox
        const loyaltyCheckbox = document.getElementById('use_loyalty_points');
        if (loyaltyCheckbox) {
            loyaltyCheckbox.addEventListener('change', function() {
                updateTotal();
            });
        }
    });
    
    function updateOrderItemsList() {
        const list = document.getElementById('orderItemsList');
        list.innerHTML = '';
        
        if (items.length === 0) {
            return;
        }
        
        const table = document.createElement('table');
        table.className = 'w-full border-collapse';
        
        // Table header
        const thead = document.createElement('thead');
        thead.innerHTML = `
            <tr class="bg-gray-50">
                <th class="border px-4 py-2 text-left">Product</th>
                <th class="border px-4 py-2 text-left">Price</th>
                <th class="border px-4 py-2 text-left">Quantity</th>
                <th class="border px-4 py-2 text-left">Subtotal</th>
                <th class="border px-4 py-2 text-center">Action</th>
            </tr>
        `;
        table.appendChild(thead);
        
        // Table body
        const tbody = document.createElement('tbody');
        items.forEach((item, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="border px-4 py-2">${item.name}</td>
                <td class="border px-4 py-2">₱${item.price.toFixed(2)}</td>
                <td class="border px-4 py-2">${item.quantity}</td>
                <td class="border px-4 py-2">₱${item.subtotal.toFixed(2)}</td>
                <td class="border px-4 py-2 text-center">
                    <button type="button" onclick="removeItem(${index})" class="text-red-600 hover:text-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });
        table.appendChild(tbody);
        
        list.appendChild(table);
    }
    
    function updateTotal() {
        let total = items.reduce((sum, item) => sum + item.subtotal, 0);
        
        // Apply loyalty discount if checkbox is checked
        const loyaltyCheckbox = document.getElementById('use_loyalty_points');
        if (loyaltyCheckbox && loyaltyCheckbox.checked && items.length > 0) {
            // Find the cheapest product
            let cheapestPrice = Number.MAX_VALUE;
            items.forEach(item => {
                if (item.price < cheapestPrice) {
                    cheapestPrice = item.price;
                }
            });
            
            // Apply discount
            total -= cheapestPrice;
            
            // Show discount info
            let discountInfo = document.getElementById('loyaltyDiscountInfo');
            if (!discountInfo) {
                discountInfo = document.createElement('div');
                discountInfo.id = 'loyaltyDiscountInfo';
                discountInfo.className = 'text-sm text-green-600 mt-2';
                document.getElementById('orderTotal').parentNode.appendChild(discountInfo);
            }
            discountInfo.textContent = `Loyalty discount: -₱${cheapestPrice.toFixed(2)}`;
        } else {
            // Remove discount info if exists
            const discountInfo = document.getElementById('loyaltyDiscountInfo');
            if (discountInfo) {
                discountInfo.remove();
            }
        }
        
        document.getElementById('orderTotal').textContent = total.toFixed(2);
    }
    
    function removeItem(index) {
        items.splice(index, 1);
        updateOrderItemsList();
        updateTotal();
        
        // Update hidden form fields
        const form = document.getElementById('orderForm');
        
        // Remove all hidden inputs
        const existingInputs = form.querySelectorAll('input[name^="products["][name$="][product_id]"]');
        existingInputs.forEach(input => {
            if (input.id.startsWith('hidden_')) {
                input.remove();
            }
        });
        
        const quantityInputs = form.querySelectorAll('input[name^="products["][name$="][quantity]"]');
        quantityInputs.forEach(input => {
            if (input.id.startsWith('hidden_')) {
                input.remove();
            }
        });
        
        // Add updated hidden inputs
        items.forEach((item, i) => {
            if (i === 0) return; // Skip first one as it's in the visible form
            
            const productIdInput = document.createElement('input');
            productIdInput.type = 'hidden';
            productIdInput.id = 'hidden_product_' + i;
            productIdInput.name = 'products[' + i + '][product_id]';
            productIdInput.value = item.productId;
            form.appendChild(productIdInput);
            
            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.id = 'hidden_quantity_' + i;
            quantityInput.name = 'products[' + i + '][quantity]';
            quantityInput.value = item.quantity;
            form.appendChild(quantityInput);
        });
    }
</script>
@endsection 