<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Acceso Restringido</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Portal del Propietario</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-lg mx-auto mt-12">
        <div class="card text-center">
            <div class="card-body py-12">
                <div class="w-20 h-20 bg-burgundy-800/10 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-user-lock text-3xl text-burgundy-800"></i>
                </div>
                <h3 class="text-xl font-heading font-bold text-navy-800 mb-3">
                    Sin Perfil de Propietario
                </h3>
                <p class="text-slate_custom-500 mb-6 leading-relaxed">
                    No tienes un perfil de propietario asociado a tu cuenta de usuario.
                    <br>Contacta al administrador del sistema para vincular tu cuenta.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ route('dashboard') }}" class="btn-primary">
                        <i class="fas fa-home mr-2"></i>Ir al Dashboard
                    </a>
                    <a href="mailto:admin@condominio.com" class="btn-secondary">
                        <i class="fas fa-envelope mr-2"></i>Contactar Administrador
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
