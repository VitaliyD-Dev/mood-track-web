<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @livewireStyles

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased h-full">
    <x-banner />

    <div class="min-h-screen flex flex-col bg-gray-100">
        @livewire('navigation-menu')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="flex-1 bg-gray-50">
            {{ $slot }}
        </main>

        @include('components.footer')
    </div>

    @include('components.chat-widget')

    @stack('modals')

    <!-- Scripts -->
    @livewireScripts

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Livewire
            window.Livewire = window.Livewire || {};
            window.Livewire.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            window.Livewire.csrfTokenName = 'X-CSRF-TOKEN';
            
            // Add CSRF token to all AJAX requests
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;

            // Initialize Alpine components
            if (window.Alpine && !window.Alpine.isStarted) {
                window.Alpine.start();
            }
        });
    </script>

    @stack('scripts')
</body>

</html>
