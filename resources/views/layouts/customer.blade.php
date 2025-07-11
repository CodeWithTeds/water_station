<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MW Water Refilling Station')</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                    },
                    transitionProperty: {
                        'width': 'width',
                        'transform': 'transform',
                    },
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .content-auto {
                content-visibility: auto;
            }
            .transition-transform {
                transition-property: transform;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 300ms;
            }
        }
    </style>
</head>
<body class="bg-white min-h-screen">
    <!-- Include sidebar as fixed element -->
    @include('components.customer-sidebar')
    
    <!-- Main Content Area with left margin to accommodate sidebar -->
    <div class="ml-64 min-h-screen flex flex-col transition-all duration-300" id="mainContent">
        <!-- Navbar -->
        @include('components.customer-navbar')
        
        <!-- Page Content -->
        <div class="flex-1 p-6">
            @yield('content')
        </div>
    </div>
</body>
</html> 