<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard - MW Water Refilling Station')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#5c7de2',
                        secondary: '#5d72af',
                        dark: '#292929',
                        gray: '#5f6372',
                        lightblue: '#95cee7',
                        darkblue: '#10265c',
                        blue: '#0d85e1',
                        darkgreen: '#2a443a',
                        white: '#eaeef0',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-white min-h-screen flex">
    <!-- Sidebar -->
    <div class="bg-dark w-64 min-h-screen flex flex-col">
        <div class="bg-primary p-4">
            <div class="flex items-center">
                <i class="fas fa-water text-white text-2xl mr-2"></i>
                <span class="text-white font-bold text-xl">MW POS System</span>
            </div>
        </div>

        <!-- Sidebar Navigation -->
        <nav class="flex-1 mt-2">
            <a href="{{ route('admin.dashboard') }}" class="block py-3 px-4 {{ request()->routeIs('admin.dashboard') ? 'bg-blue' : '' }} text-white hover:bg-secondary transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    <span>Dashboard</span>
                </div>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="block py-3 px-4 {{ request()->routeIs('admin.orders.*') ? 'bg-blue' : '' }} text-white hover:bg-secondary transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-shopping-bag mr-3"></i>
                    <span>Orders</span>
                    @php
                        $pendingOrdersCount = App\Models\Order::where('status', 'pending')->count();
                    @endphp
                    @if($pendingOrdersCount > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $pendingOrdersCount }}</span>
                    @endif
                </div>
            </a>
            <a href="{{ route('admin.sales.index') }}" class="block py-3 px-4 text-white hover:bg-secondary transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-shopping-cart mr-3"></i>
                    <span>Sales</span>
                </div>
            </a>
            <a href="{{ route('admin.products.index') }}" class="block py-3 px-4 text-white hover:bg-secondary transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-tools mr-3"></i>
                    <span>Maintenance</span>
                </div>
            </a>
            <a href="#" class="block py-3 px-4 text-white hover:bg-secondary transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-chart-bar mr-3"></i>
                    <span>Sales Reports</span>
                </div>
            </a>
            <a href="#" class="block py-3 px-4 text-white hover:bg-secondary transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-bell mr-3"></i>
                    <span>Notification</span>
                </div>
            </a>
            <a href="#" class="block py-3 px-4 text-white hover:bg-secondary transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-boxes mr-3"></i>
                    <span>Inventory</span>
                </div>
            </a>
            <a href="#" class="block py-3 px-4 text-white hover:bg-secondary transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-cog mr-3"></i>
                    <span>Settings</span>
                </div>
            </a>
            <a href="#" class="block py-3 px-4 text-white hover:bg-secondary transition-colors">
                <div class="flex items-center">
                    <i class="fas fa-info-circle mr-3"></i>
                    <span>System Info</span>
                </div>
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1">
        <!-- Top Navigation -->
        <div class="bg-white shadow-md p-4">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-semibold text-gray-800">@yield('header', 'MW Point of sale (POS) System -Super Admin')</h1>
                <div class="flex items-center">
                    @if(Auth::check())
                        <span class="mr-4">{{ Auth::user()->name }}</span>
                    @else
                        <span class="mr-4 text-gray-500">Not logged in</span>
                    @endif
                    <form method="POST" action="{{ route('admin.logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-600 hover:text-gray-800 focus:outline-none">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <main class="p-6">
            @yield('content')
        </main>
    </div>
</body>
</html> 