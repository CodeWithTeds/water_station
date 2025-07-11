@extends('layouts.app')

@section('title', 'Register - MW Water Refilling Station')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-4xl">
        <x-auth-card>
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Register</h2>
            
            <form action="{{ route('register') }}" method="POST">
                @csrf
                
                <x-input 
                    name="fullname" 
                    label="Full name" 
                    required="true" 
                    placeholder="Enter your Fullname"
                />
                
                <x-input 
                    name="address" 
                    label="Address" 
                    required="true" 
                    placeholder="Enter your address"
                />
                
                <x-input 
                    name="contact_no" 
                    label="Contact no." 
                    required="true" 
                    placeholder="Enter your contact no."
                />
                
                <x-input 
                    type="password" 
                    name="password" 
                    label="Password" 
                    required="true" 
                    placeholder="Enter your password"
                />
                
                <x-input 
                    type="password" 
                    name="password_confirmation" 
                    label="Confirm password" 
                    required="true" 
                    placeholder="Confirm your password"
                />
                
                <div class="mb-6">
                    <x-button>
                        Register
                    </x-button>
                </div>
                
                <div class="text-center text-gray-600">
                    Already have an account? <a href="{{ route('login') }}" class="text-primary hover:underline">Login</a>
                </div>
            </form>
        </x-auth-card>
    </div>
</div>
@endsection 