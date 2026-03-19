<nav class="bg-white border-b border-slate_custom-200 sticky top-0 z-20">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-4">
                <!-- Mobile menu button -->
                <button @click="mobileSidebar = !mobileSidebar" class="lg:hidden text-navy-800 hover:text-burgundy-800 p-2">
                    <i class="fas fa-bars text-lg"></i>
                </button>

                <!-- Breadcrumb / Page info -->
                <div class="hidden sm:flex items-center text-sm text-slate_custom-400">
                    <i class="fas fa-home mr-2"></i>
                    <span>{{ config('app.name') }}</span>
                </div>
            </div>

            <!-- Right side -->
            <div class="flex items-center gap-4">
                <!-- Notifications -->
                <button class="relative text-slate_custom-400 hover:text-navy-800 transition">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute -top-1 -right-1 w-4 h-4 bg-burgundy-800 text-white text-[10px] font-bold rounded-full flex items-center justify-center">3</span>
                </button>

                <!-- User Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-slate_custom-100 transition">
                        <div class="w-8 h-8 bg-gradient-to-br from-burgundy-800 to-navy-800 rounded-full flex items-center justify-center">
                            <span class="text-white text-xs font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</span>
                        </div>
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-semibold text-navy-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate_custom-400">{{ Auth::user()->roles->first()?->name ?? 'Usuario' }}</p>
                        </div>
                        <i class="fas fa-chevron-down text-xs text-slate_custom-400 ml-1 transition-transform duration-200" :class="open && 'rotate-180'"></i>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-slate_custom-200 py-1 z-50"
                         style="display: none;">

                        <div class="px-4 py-3 border-b border-slate_custom-200">
                            <p class="text-sm font-semibold text-navy-800">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate_custom-400">{{ Auth::user()->email }}</p>
                        </div>

                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-slate_custom-500 hover:bg-slate_custom-100 hover:text-navy-800 transition">
                            <i class="fas fa-user w-4 text-center"></i>
                            Mi Perfil
                        </a>

                        <div class="border-t border-slate_custom-200 mt-1 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-3 w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                    <i class="fas fa-sign-out-alt w-4 text-center"></i>
                                    Cerrar Sesión
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
