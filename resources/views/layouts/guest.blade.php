<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Administracion Condominio') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800|rubik:400,500&display=swap" rel="stylesheet" />

        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-body antialiased">
        <div class="min-h-screen flex">
            <!-- Left Panel - Branding -->
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-navy-800 via-navy-900 to-burgundy-900 relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-20 left-10 w-64 h-64 bg-burgundy-800 rounded-full filter blur-3xl"></div>
                    <div class="absolute bottom-20 right-10 w-96 h-96 bg-navy-600 rounded-full filter blur-3xl"></div>
                </div>
                <div class="relative z-10 flex flex-col justify-center items-center w-full px-12">
                    <div class="w-20 h-20 bg-burgundy-800 rounded-2xl flex items-center justify-center mb-8 shadow-2xl">
                        <span class="text-white font-heading font-bold text-3xl">AI</span>
                    </div>
                    <h1 class="text-4xl font-heading font-bold text-white text-center mb-4">
                        Administradora Integral
                    </h1>
                    <p class="text-lg text-slate_custom-300 text-center max-w-md leading-relaxed">
                        Sistema de Administración de Condominios
                    </p>
                    <div class="mt-12 grid grid-cols-2 gap-6 text-center">
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <i class="fas fa-building text-2xl text-burgundy-300 mb-2"></i>
                            <p class="text-white/80 text-sm">Gestión de Edificios</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <i class="fas fa-hand-holding-usd text-2xl text-burgundy-300 mb-2"></i>
                            <p class="text-white/80 text-sm">Cobranza</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <i class="fas fa-users text-2xl text-burgundy-300 mb-2"></i>
                            <p class="text-white/80 text-sm">Personal</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4">
                            <i class="fas fa-chart-bar text-2xl text-burgundy-300 mb-2"></i>
                            <p class="text-white/80 text-sm">Informes</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel - Form -->
            <div class="w-full lg:w-1/2 flex flex-col justify-center items-center px-6 sm:px-12 bg-white">
                <!-- Mobile logo -->
                <div class="lg:hidden mb-8">
                    <div class="w-16 h-16 bg-burgundy-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <span class="text-white font-heading font-bold text-2xl">AI</span>
                    </div>
                    <h1 class="text-xl font-heading font-bold text-navy-800 text-center">Administradora Integral</h1>
                </div>

                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>

                <p class="mt-12 text-xs text-slate_custom-400 text-center">
                    &copy; {{ date('Y') }} Administradora Integral E.L.B., C.A.
                </p>
            </div>
        </div>
    </body>
</html>
