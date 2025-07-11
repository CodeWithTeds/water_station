<div class="flex flex-col md:flex-row w-full max-w-6xl mx-auto shadow-xl rounded-lg overflow-hidden">
    <!-- Brand section -->
    <div class="bg-primary text-white p-8 md:w-1/2 flex flex-col justify-center items-center">
        <div class="mb-8 w-full max-w-md">
            <img src="{{ asset('images/login-logo.jpg') }}" alt="MW Waters Logo" class="w-full">
        </div>
        <div class="text-center">
            <h1 class="text-3xl font-bold mb-2 italic">Welcome to MW Water Refilling Station</h1>
            <p class="text-xl">Your trusted source of clean, safe, and refreshing drinking water.</p>
        </div>
    </div>

    <!-- Form section -->
    <div class="bg-white p-8 md:w-1/2">
        <div class="max-w-md mx-auto">
            {{ $slot }}
        </div>
    </div>
</div> 