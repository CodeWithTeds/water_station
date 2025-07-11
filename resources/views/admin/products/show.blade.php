@extends('layouts.admin')

@section('title', 'Product Details')

@section('content')
<div class="max-w-2xl mx-auto py-10">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-extrabold text-gray-800">Product Details</h1>
        <a href="{{ route('admin.products.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-5 py-2 rounded shadow flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> <span>Back to List</span>
        </a>
    </div>
    <div class="bg-white shadow-2xl border border-blue-200 rounded-2xl p-10 relative">
        <div class="absolute -top-10 left-1/2 -translate-x-1/2">
            <div class="bg-blue-100 rounded-full w-20 h-20 flex items-center justify-center shadow-lg">
                <i class="fas fa-box-open text-blue-500 text-4xl"></i>
            </div>
        </div>
        <div class="mt-12 text-center">
            <div class="flex items-center justify-center gap-3 mb-2">
                @if($product->is_active)
                    <span class="inline-block w-3 h-3 bg-blue-500 rounded-full"></span>
                @else
                    <span class="inline-block w-3 h-3 bg-red-500 rounded-full"></span>
                @endif
                <span class="text-2xl font-bold text-gray-800">{{ $product->name }}</span>
                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $product->is_active ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700' }}">
                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="text-gray-500 italic mb-6">{{ $product->description }}</div>
        </div>
        <div class="grid grid-cols-2 gap-8 mt-8 mb-8">
            <div class="text-center">
                <span class="block text-gray-500 text-xs mb-1">Price</span>
                <div class="font-extrabold text-3xl text-green-600">₱{{ number_format($product->price, 2) }}</div>
            </div>
            <div class="text-center">
                <span class="block text-gray-500 text-xs mb-1">Stock Quantity</span>
                <div class="font-extrabold text-3xl text-blue-700">{{ $product->stock_quantity }}</div>
            </div>
        </div>
        <div class="border-t pt-6 flex flex-col md:flex-row justify-center gap-4 mt-6">
            <a href="{{ route('admin.products.edit', $product->id) }}" class="bg-yellow-400 hover:bg-yellow-500 text-white px-6 py-2 rounded shadow text-lg font-semibold flex items-center gap-2 justify-center"><i class="fas fa-edit"></i> Edit</a>
            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Delete this product?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded shadow text-lg font-semibold flex items-center gap-2 justify-center"><i class="fas fa-trash"></i> Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection 