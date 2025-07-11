<!-- Customer Sidebar Component -->
@php
    use Illuminate\Support\Facades\Auth;
@endphp

<style>
    .notification-item {
        transition: opacity 0.3s ease;
    }
    .notification-item span.inline-block {
        transition: opacity 0.3s ease;
    }
</style>

<div id="sidebar" class="w-64 bg-[#323233] text-white h-screen fixed left-0 top-0 flex flex-col transition-all duration-300">
    <!-- Logo and Toggle Button -->
    <div class="flex items-center justify-between p-4 border-b border-gray-600 bg-[#323233]">
        <div class="flex items-center">
            <img src="{{ asset('images/login-logo.jpg') }}" alt="MW Waters Logo" class="h-16 w-auto rounded-full">
        </div>
        <!-- Toggle Button -->
        <button id="sidebarToggle" class="text-white hover:text-gray-300 focus:outline-none">
            <svg id="toggleIcon" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path>
            </svg>
        </button>
    </div>
    
    <!-- Navigation Links -->
    <nav class="flex-grow">
        <a href="{{ route('customer.dashboard') }}" class="flex items-center px-6 py-4 bg-[#323233] {{ request()->routeIs('customer.dashboard') ? 'bg-[#4e7af4]' : 'hover:bg-[#346eec]' }}">
            <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
            </svg>
            <span class="sidebar-text">Dashboard</span>
        </a>
        <a href="{{ route('customer.products') }}" class="flex items-center px-6 py-4 {{ request()->routeIs('customer.products') ? 'bg-[#4e7af4]' : 'bg-[#323233] hover:bg-[#346eec]' }}">
            <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span class="sidebar-text">Products</span>
        </a>
        <a href="{{ route('customer.history') }}" class="flex items-center px-6 py-4 {{ request()->routeIs('customer.history') ? 'bg-[#4e7af4]' : 'bg-[#323233] hover:bg-[#346eec]' }}">
            <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <span class="sidebar-text">History</span>
        </a>

        <!-- Notifications Section with Toggle -->
        <div class="px-6 py-4 border-t border-gray-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                    <span class="sidebar-text">Notifications</span>
                    @if(isset($notificationCount) && $notificationCount > 0)
                        <span class="ml-2 bg-blue-500 text-white text-xs font-semibold rounded-full px-2 py-0.5">{{ $notificationCount }}</span>
                    @endif
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer notifications-toggle" checked>
                    <div class="w-9 h-5 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                </label>
            </div>
            <div class="mt-3 pl-9 text-sm text-gray-300 sidebar-text notifications-content">
                @php
                    $customer = Auth::guard('customer')->user();
                    $notifications = [];
                    $recentOrders = [];
                    
                    if ($customer) {
                        // Get recent orders with different statuses
                        $pendingOrders = App\Models\Order::where('customer_id', $customer->id)
                            ->where('status', 'pending')
                            ->count();
                            
                        $processingOrders = App\Models\Order::where('customer_id', $customer->id)
                            ->where('status', 'processing')
                            ->count();
                            
                        $completedOrders = App\Models\Order::where('customer_id', $customer->id)
                            ->where('status', 'completed')
                            ->where('created_at', '>=', now()->subDays(2))
                            ->count();
                            
                        // Add notifications based on order status
                        if ($pendingOrders > 0) {
                            $notifications[] = "You have {$pendingOrders} pending order(s)";
                        }
                        
                        if ($processingOrders > 0) {
                            $notifications[] = "You have {$processingOrders} order(s) being processed";
                        }
                        
                        if ($completedOrders > 0) {
                            $notifications[] = "{$completedOrders} order(s) completed recently";
                        }
                        
                        // Get 3 most recent orders
                        $recentOrders = App\Models\Order::where('customer_id', $customer->id)
                            ->orderBy('created_at', 'desc')
                            ->take(3)
                            ->get();
                    }
                    
                    $notificationCount = count($notifications);
                @endphp
                
                @if(count($notifications) > 0)
                    <div class="space-y-2">
                        <div class="flex justify-between items-center mb-2">
                            <p class="text-xs font-medium text-gray-400">NOTIFICATIONS</p>
                            <button id="markAllRead" class="text-xs text-blue-400 hover:text-blue-300">Mark all read</button>
                        </div>
                        @foreach($notifications as $index => $notification)
                            <div class="flex items-start notification-item">
                                @if(strpos($notification, 'pending') !== false)
                                    <span class="inline-block w-2 h-2 bg-yellow-500 rounded-full mr-2 mt-1.5"></span>
                                    <span class="text-yellow-200">{{ $notification }}</span>
                                @elseif(strpos($notification, 'processing') !== false)
                                    <span class="inline-block w-2 h-2 bg-blue-500 rounded-full mr-2 mt-1.5"></span>
                                    <span class="text-blue-200">{{ $notification }}</span>
                                @elseif(strpos($notification, 'completed') !== false)
                                    <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2 mt-1.5"></span>
                                    <span class="text-green-200">{{ $notification }}</span>
                                @else
                                    <span class="inline-block w-2 h-2 bg-blue-500 rounded-full mr-2 mt-1.5"></span>
                                    <span>{{ $notification }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p>No new notifications</p>
                @endif
                
                @if(count($recentOrders) > 0)
                    <div class="mt-4 pt-3 border-t border-gray-600">
                        <p class="text-xs font-medium text-gray-400 mb-2">RECENT ORDERS</p>
                        <div class="space-y-2">
                            @foreach($recentOrders as $order)
                                <a href="{{ route('customer.history') }}" class="flex items-start hover:bg-gray-700 p-1 rounded transition-colors">
                                    <div class="w-full">
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs font-medium">Order #{{ $order->id }}</span>
                                            <span class="text-xs 
                                                @if($order->status === 'completed') text-green-400
                                                @elseif($order->status === 'pending') text-yellow-400
                                                @elseif($order->status === 'processing') text-blue-400
                                                @else text-red-400
                                                @endif">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-400">{{ $order->created_at->format('M d, Y') }}</p>
                                    </div>
                                </a>
                            @endforeach
                            
                            <a href="{{ route('customer.history') }}" class="block text-center text-xs text-blue-400 hover:text-blue-300 mt-2 py-1">
                                View all orders →
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Loyalty Section -->
        <div class="px-6 py-4 border-t border-gray-600">
            <div class="flex items-center">
                <svg class="h-6 w-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                </svg>
                <span class="sidebar-text">Loyalty Points</span>
            </div>
            <div class="mt-3 bg-gray-700 rounded-lg p-3">
                @php
                    $customer = Auth::guard('customer')->user();
                    $targetPoints = 10; // 10 orders = 1 free product
                    
                    // Get completed orders count directly
                    $completedOrdersCount = 0;
                    if ($customer) {
                        $completedOrdersCount = App\Models\Order::where('customer_id', $customer->id)
                            ->where('status', 'completed')
                            ->count();
                            
                        // Update customer loyalty points if needed
                        if ($customer->loyalty_points != $completedOrdersCount) {
                            $customer->loyalty_points = $completedOrdersCount;
                            $customer->save();
                        }
                    }
                    
                    $pointsToNextFree = $completedOrdersCount % $targetPoints;
                    $progressPercentage = ($pointsToNextFree / $targetPoints) * 100;
                    $freeProductsAvailable = floor($completedOrdersCount / $targetPoints);
                @endphp
                
                <div class="flex justify-between mb-2">
                    <span class="text-xs text-gray-300 sidebar-text">Loyalty Vouchers</span>
                    <span class="text-sm font-bold text-blue-400">{{ $pointsToNextFree }} / {{ $targetPoints }} pts</span>
                </div>
                <div class="w-full bg-gray-600 rounded-full h-2.5">
                    <div class="bg-blue-500 h-2.5 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                </div>
                <div class="flex justify-between mt-2">
                    <span class="text-xs text-gray-300 sidebar-text">{{ $pointsToNextFree }} pts</span>
                    <span class="text-xs text-gray-300">{{ round($progressPercentage) }}% completed</span>
                </div>
                
                @if($freeProductsAvailable > 0)
                    <div class="mt-3 bg-blue-900 rounded-lg p-2 text-center">
                        <span class="text-sm font-semibold text-white">You have {{ $freeProductsAvailable }} free product(s) available!</span>
                        <p class="text-xs text-blue-200 mt-1">Use at checkout</p>
                    </div>
                @endif
                
                <div class="mt-3 text-xs text-gray-400">
                    <p>• Earn 1 point for each completed order</p>
                    <p>• Get 1 free product for every 10 points</p>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- User Profile Section -->
    <div class="border-t border-gray-600 p-4">
        <div class="flex items-center">
            <div class="h-10 w-10 rounded-full bg-gray-600 flex items-center justify-center">
                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div class="ml-3 sidebar-text">
                <p class="font-medium text-sm">{{ Auth::guard('customer')->user()->fullname }}</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                    @csrf
                    <button type="submit" class="text-xs text-gray-300 hover:text-white">
                        Sign out
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Floating button to show sidebar when hidden -->
<button id="showSidebar" class="fixed top-4 left-4 bg-[#4e7af4] p-3 rounded-full shadow-lg text-white hidden z-50 hover:bg-[#346eec] focus:outline-none">
    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
    </svg>
</button>

<!-- Script for sidebar toggle -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const showSidebar = document.getElementById('showSidebar');
        const mainContent = document.querySelector('.ml-64');
        let sidebarHidden = false;
        
        // Function to hide sidebar
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            
            // Show the floating button
            showSidebar.classList.remove('hidden');
            
            // Adjust main content to take full width
            if (mainContent) {
                mainContent.classList.remove('ml-64');
                mainContent.classList.add('ml-0');
            }
            
            sidebarHidden = true;
        });
        
        // Function to show sidebar
        showSidebar.addEventListener('click', function() {
            sidebar.classList.remove('-translate-x-full');
            
            // Hide the floating button
            showSidebar.classList.add('hidden');
            
            // Restore main content margin
            if (mainContent) {
                mainContent.classList.remove('ml-0');
                mainContent.classList.add('ml-64');
            }
            
            sidebarHidden = false;
        });
        
        // Notification toggle functionality
        const notificationToggle = document.querySelector('.notifications-toggle');
        const notificationContent = document.querySelector('.notifications-content');
        
        if (notificationToggle && notificationContent) {
            notificationToggle.addEventListener('change', function() {
                if (this.checked) {
                    localStorage.setItem('notifications_enabled', 'true');
                    notificationContent.classList.remove('hidden');
                } else {
                    localStorage.setItem('notifications_enabled', 'false');
                    notificationContent.classList.add('hidden');
                }
            });
            
            // Check local storage on page load
            const notificationsEnabled = localStorage.getItem('notifications_enabled') !== 'false';
            notificationToggle.checked = notificationsEnabled;
            
            if (!notificationsEnabled) {
                notificationContent.classList.add('hidden');
            }
        }
        
        // Mark all notifications as read
        const markAllReadBtn = document.getElementById('markAllRead');
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function() {
                const notificationItems = document.querySelectorAll('.notification-item');
                notificationItems.forEach(item => {
                    // Add a class to show it's been read
                    item.classList.add('opacity-50');
                    
                    // Remove the notification dot
                    const dot = item.querySelector('span.inline-block');
                    if (dot) {
                        dot.classList.add('opacity-0');
                    }
                });
                
                // Update the notification count badge
                const badge = document.querySelector('.ml-2.bg-blue-500');
                if (badge) {
                    badge.classList.add('hidden');
                }
                
                // Store in localStorage that notifications were read
                localStorage.setItem('notifications_last_read', new Date().toISOString());
            });
        }
    });
</script> 