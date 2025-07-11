<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'delivery_address' => 'required|string',
            'type' => 'required|string',
            'use_loyalty_points' => 'nullable|boolean',
        ];
    }
    
    /**
     * Get the prepared order data from the request.
     *
     * @return array
     */
    public function getOrderData(): array
    {
        return [
            'customer_id' => auth()->guard('customer')->id(),
            'delivery_address' => $this->delivery_address,
            'order_type' => $this->type,
            'payment_method' => $this->payment_method ?? 'Cash',
            'payment_status' => 'pending',
            'status' => 'pending',
        ];
    }
    
    /**
     * Check if the user wants to use loyalty points.
     *
     * @return bool
     */
    public function useLoyaltyPoints(): bool
    {
        return $this->has('use_loyalty_points') && $this->use_loyalty_points;
    }
} 