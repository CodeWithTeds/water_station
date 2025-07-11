@extends('layouts.app')

@section('title', 'Login - MW Water Refilling Station')

@section('content')
<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-4xl">
        <x-auth-card>
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">Login</h2>
            
            @if ($errors->any())
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                    {{ $errors->first('login') }}
                </div>
            @endif
            
            <form action="{{ route('login') }}" method="POST">
                @csrf
                
                <x-input 
                    name="contact_no" 
                    label="Contact Number" 
                    required="true"
                    placeholder="Enter your contact number"
                />
                
                <x-input 
                    type="password" 
                    name="password" 
                    label="Password" 
                    required="true"
                    placeholder="Enter your password" 
                />
                
                <div class="mb-6">
                    <x-button>
                        Login
                    </x-button>
                </div>
                
                <div class="text-center text-gray-600">
                    Don't have an account? <a href="{{ route('register') }}" class="text-primary hover:underline">Register</a>
                </div>
            </form>
        </x-auth-card>
    </div>
</div>
@endsection 