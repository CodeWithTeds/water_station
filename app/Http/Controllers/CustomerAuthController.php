<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerLoginRequest;
use App\Http\Requests\CustomerRegistrationRequest;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerAuthController extends Controller
{
    protected CustomerService $customerService;
    
    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }
    
    public function showLoginForm()
    {
        return view('customer.login');
    }

    public function login(CustomerLoginRequest $request)
    {
        if (Auth::guard('customer')->attempt($request->credentials())) {
            $request->session()->regenerate();
            return redirect()->intended('/customer/dashboard');
        }

        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ]);
    }

    public function showRegisterForm()
    {
        return view('customer.register');
    }

    public function register(CustomerRegistrationRequest $request)
    {
        $customer = $this->customerService->createCustomer($request->getCustomerData());

        if (!$customer) {
            return back()->withErrors([
                'registration' => 'There was a problem with your registration. Please try again.',
            ])->withInput();
        }

        return redirect()->route('login')->with('success', 'Registration successful! Please login.');
    }

    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
    
    public function dashboard()
    {
        $customer = Auth::guard('customer')->user();
        $loyaltyInfo = $this->customerService->getLoyaltyInfo($customer->id);
        $recentOrders = $this->customerService->getRecentOrders($customer->id, 3);
        $products = \App\Models\Product::where('is_active', true)->get();
        
        return view('customer.dashboard', compact('loyaltyInfo', 'recentOrders', 'products'));
    }
} 