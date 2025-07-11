<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerRegistrationRequest extends FormRequest
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
            'fullname' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_no' => 'required|string|unique:customers',
            'password' => 'required|string|min:8|confirmed',
        ];
    }
    
    /**
     * Get the customer data from the request.
     *
     * @return array
     */
    public function getCustomerData(): array
    {
        return [
            'fullname' => $this->fullname,
            'address' => $this->address,
            'contact_no' => $this->contact_no,
            'password' => $this->password,
        ];
    }
} 