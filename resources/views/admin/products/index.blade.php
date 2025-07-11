@extends('layouts.admin')

@section('title', 'Product Maintenance')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">List of Jar Types & Pricing</h1>
        <a href="{{ route('admin.products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center">
            <span class="text-lg font-bold mr-2">+</span> Create New
        </a>
    </div>
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif
    <div class="mb-4 flex justify-end">
        <input id="product-search" type="text" placeholder="Search products..." class="border border-gray-300 rounded px-3 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-blue-400" />
    </div>
    <div class="bg-white shadow-lg rounded-xl overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 rounded-xl overflow-hidden">
            <thead class="bg-gradient-to-r from-blue-100 to-blue-200 sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">#</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Date Created</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Name</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Description</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Price</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Stock</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-bold text-blue-700 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody id="product-table-body" class="bg-white divide-y divide-gray-100">
                @forelse($products as $i => $product)
                <tr class="transition hover:bg-blue-50 {{ $i % 2 === 0 ? 'bg-gray-50' : 'bg-white' }} {{ $product->is_active ? 'border-l-4 border-blue-400' : 'border-l-4 border-red-400' }}">
                    <td class="px-4 py-3">{{ $i+1 }}</td>
                    <td class="px-4 py-3">{{ $product->created_at->format('Y-m-d H:i:s') }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-800 flex items-center gap-2">
                        @if($product->is_active)
                            <span class="inline-block w-2 h-2 bg-blue-500 rounded-full"></span>
                        @else
                            <span class="inline-block w-2 h-2 bg-red-500 rounded-full"></span>
                        @endif
                        {{ $product->name }}
                    </td>
                    <td class="px-4 py-3 italic text-gray-600">{{ $product->description }}</td>
                    <td class="px-4 py-3">₱{{ number_format($product->price, 2) }}</td>
                    <td class="px-4 py-3">{{ $product->stock_quantity }}</td>
                    <td class="px-4 py-3">
                        @if($product->is_active)
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-bold">Active</span>
                        @else
                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-bold">Inactive</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex space-x-2">
                     
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white px-3 py-1 rounded text-xs shadow">Edit</a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs shadow">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-gray-500">No products found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<script>
    // Simple client-side search
    document.getElementById('product-search').addEventListener('input', function() {
        const search = this.value.toLowerCase();
        document.querySelectorAll('#product-table-body tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(search) ? '' : 'none';
        });
    });
</script>
@endsection 