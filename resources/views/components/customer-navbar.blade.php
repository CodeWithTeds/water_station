<!-- Customer Top Navigation -->
<nav class="bg-[#313131] shadow-sm p-4 flex justify-between items-center">
    <div class="flex items-center">
        <h1 class="text-2xl font-bold text-[#ffffff] ml-4">MW Waters</h1>
    </div>

    <div class="flex items-center">
        <span class="mr-4 text-[#313131] font-medium">{{ Auth::guard('customer')->user()->fullname }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-[#313131] hover:text-gray-800 focus:outline-none">
                Logout
            </button>
        </form>
    </div>
</nav> 