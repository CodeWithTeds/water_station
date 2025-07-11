<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerLoginRequest extends FormRequest
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
            'contact_no' => 'required',
            'password' => 'required',
        ];
    }
    
    /**
     * Get the credentials for authentication.
     *
     * @return array
     */
    public function credentials(): array
    {
        return [
            'contact_no' => $this->contact_no,
            'password' => $this->password,
        ];
    }
} 