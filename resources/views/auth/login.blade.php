<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-heading font-bold text-navy-800">Iniciar Sesión</h2>
        <p class="text-sm text-slate_custom-400 mt-1">Ingresa tus credenciales para acceder al sistema</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Correo Electrónico')" class="text-navy-800 font-semibold text-sm" />
            <div class="relative mt-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate_custom-400">
                    <i class="fas fa-envelope"></i>
                </span>
                <x-text-input id="email" class="block w-full pl-10" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="correo@ejemplo.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-5">
            <x-input-label for="password" :value="__('Contraseña')" class="text-navy-800 font-semibold text-sm" />
            <div class="relative mt-1">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate_custom-400">
                    <i class="fas fa-lock"></i>
                </span>
                <x-text-input id="password" class="block w-full pl-10"
                                type="password"
                                name="password"
                                required autocomplete="current-password"
                                placeholder="••••••••" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-5">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate_custom-200 text-burgundy-800 shadow-sm focus:ring-burgundy-500" name="remember">
                <span class="ms-2 text-sm text-slate_custom-400">{{ __('Recordarme') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-burgundy-800 hover:text-burgundy-700 font-medium" href="{{ route('password.request') }}">
                    {{ __('¿Olvidaste tu contraseña?') }}
                </a>
            @endif
        </div>

        <div class="mt-6">
            <button type="submit" class="btn-primary w-full justify-center py-3">
                <i class="fas fa-sign-in-alt mr-2"></i>
                {{ __('Iniciar Sesión') }}
            </button>
        </div>
    </form>
</x-guest-layout>
