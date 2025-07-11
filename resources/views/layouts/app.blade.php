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
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .content-auto {
                content-visibility: auto;
            }
        }
    </style>
</head>
<body class="bg-white min-h-screen">
    <main>
        @yield('content')
    </main>
</body>
</html> 