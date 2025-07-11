<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminEditSaleRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'delivery_address' => 'required|string|max:255',
            'type' => 'required|in:For Delivery,For Pickup',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_status' => 'required|in:pending,paid,failed',
        ];
    }
} 