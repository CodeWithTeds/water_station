@extends('layouts.admin')

@section('title', 'Create Product')

@section('content')
<div class="max-w-xl mx-auto py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Create New Product</h1>
    <form action="{{ route('admin.products.store') }}" method="POST" class="bg-white shadow rounded-lg p-6 space-y-6">
        @csrf
        <div>
            <label class="block text-gray-700 font-medium mb-2">Image</label>
            <label class="block text-gray-700 font-medium mb-2">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required>
            @error('name')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-2">Description</label>
            <textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description') }}</textarea>
            @error('description')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-gray-700 font-medium mb-2">Price (₱)</label>
                <input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" class="w-full border rounded px-3 py-2" required>
                @error('price')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2">Stock Quantity</label>
                <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0" class="w-full border rounded px-3 py-2" required>
                @error('stock_quantity')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
            </div>
        </div>
        <div>
            <label class="block text-gray-700 font-medium mb-2">Status</label>
            <select name="is_active" class="w-full border rounded px-3 py-2">
                <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('is_active', 1) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('is_active')<div class="text-red-500 text-sm mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="flex justify-end">
            <a href="{{ route('admin.products.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded mr-2">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">Create</button>
        </div>
    </form>
</div>
@endsection 