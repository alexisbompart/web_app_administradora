<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@hasSection('title')@yield('title') — @endif{{ config('app.name', 'Administracion Condominio') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800|rubik:400,500&display=swap" rel="stylesheet" />

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-body antialiased">
        <div x-data="{ sidebarOpen: true, mobileSidebar: false }" class="min-h-screen flex bg-slate_custom-100">

            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Main Content -->
            <div class="flex-1 flex flex-col min-h-screen transition-all duration-300"
                 :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-20'">

                <!-- Top Navigation -->
                @include('layouts.navigation')

                <!-- Page Heading -->
                @hasSection('header')
                    <header class="bg-white border-b border-slate_custom-200">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            @yield('header')
                        </div>
                    </header>
                @elseif(isset($header))
                    <header class="bg-white border-b border-slate_custom-200">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    @hasSection('content')
                        @yield('content')
                    @else
                        {{ $slot ?? '' }}
                    @endif
                </main>

                <!-- Footer -->
                <footer class="bg-navy-800 text-white/50 text-sm py-4 px-6 text-center">
                    <p>&copy; {{ date('Y') }} Administradora Integral E.L.B., C.A. - Todos los derechos reservados.</p>
                </footer>
            </div>
        </div>

        <!-- Mobile sidebar overlay -->
        @stack('scripts')
        <div x-show="mobileSidebar" x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="mobileSidebar = false"
             class="fixed inset-0 bg-black/50 z-30 lg:hidden" style="display: none;"></div>
    </body>
</html>
