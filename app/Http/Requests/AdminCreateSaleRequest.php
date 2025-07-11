<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminCreateSaleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'delivery_address' => 'required|string',
            'order_type' => 'required|in:For Delivery,For Pickup',
            'payment_method' => 'required|in:Cash,GCash,Bank Transfer',
            'payment_status' => 'required|in:pending,paid,failed',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.required' => 'Customer name is required.',
            'delivery_address.required' => 'Delivery address is required.',
            'order_type.required' => 'Order type is required.',
            'payment_method.required' => 'Payment method is required.',
            'payment_status.required' => 'Payment status is required.',
            'items.required' => 'Please select at least one product.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }
} 