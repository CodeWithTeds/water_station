@extends('layouts.admin')

@section('title', 'Edit Product')

@section('content')
<div class="max-w-xl mx-auto py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Product</h1>
        <a href="{{ route('admin.products.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded shadow">← Back to List</a>
    </div>
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" class="bg-white shadow-xl border border-blue-100 rounded-lg p-6 space-y-6">
        @csrf
        @method('PUT')
        <div>
            <label class="block text-gray-700 font-medium mb-2">Name</label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full border rounded px-3 py-2" required>
            @error('name')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-2">Description</label>
            <textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description', $product->description) }}</textarea>
            @error('description')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Price (₱)</label>
                <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" class="w-full border rounded px-3 py-2" required>
                @error('price')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2">Stock Quantity</label>
                <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" class="w-full border rounded px-3 py-2" required>
                @error('stock_quantity')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-2">Status</label>
            <select name="is_active" class="w-full border rounded px-3 py-2">
                <option value="1" {{ old('is_active', $product->is_active) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('is_active', $product->is_active) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('is_active')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="flex justify-between items-center mt-8">
            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Delete this product?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded shadow">Delete</button>
            </form>
            <div>
                <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded mr-2">Cancel</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">Update</button>
            </div>
        </div>
    </form>
</div>
@endsection 