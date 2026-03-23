<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">
                    {{ isset($role) ? 'Editar Rol' : 'Crear Rol' }}
                </h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    {{ isset($role) ? 'Modificar rol y sus permisos' : 'Registrar nuevo rol en el sistema' }}
                </p>
            </div>
            <a href="{{ route('admin.roles.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    <form action="{{ isset($role) ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST">
        @csrf
        @if(isset($role))
            @method('PUT')
        @endif

        {{-- Datos del Rol --}}
        <div class="card mb-6">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-shield-alt mr-2 text-burgundy-800"></i>
                    {{ isset($role) ? 'Datos del Rol' : 'Nuevo Rol' }}
                </h3>
            </div>
            <div class="card-body">
                <div class="max-w-md">
                    <label for="name" class="block text-sm font-medium text-navy-800 mb-1">Nombre del Rol <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name"
                           value="{{ old('name', $role->name ?? '') }}"
                           class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                           placeholder="Ej: administrador, gerente, etc."
                           required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-slate_custom-400 mt-1">Use nombres en minusculas separados por guiones. Ej: gerente-operaciones</p>
                </div>
            </div>
        </div>

        {{-- Permisos --}}
        <div class="card mb-6">
            <div class="card-header flex items-center justify-between">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-key mr-2 text-burgundy-800"></i>Asignar Permisos
                </h3>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="toggleAll(true)" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                        <i class="fas fa-check-double mr-1"></i>Marcar todos
                    </button>
                    <span class="text-slate_custom-300">|</span>
                    <button type="button" onclick="toggleAll(false)" class="text-xs text-slate_custom-500 hover:text-slate_custom-700 font-medium">
                        <i class="fas fa-times mr-1"></i>Desmarcar todos
                    </button>
                </div>
            </div>
            <div class="card-body">
                @php
                    $rolePermissions = isset($role) ? $role->permissions->pluck('name')->toArray() : [];
                    $grouped = $permissions->groupBy(function ($p) {
                        return explode('.', $p->name)[0] ?? 'general';
                    });
                    $groupIcons = [
                        'sistema' => 'fas fa-cog',
                        'condominio' => 'fas fa-building',
                        'personal' => 'fas fa-id-badge',
                        'proveedores' => 'fas fa-truck',
                        'fondos' => 'fas fa-piggy-bank',
                        'cobranza' => 'fas fa-hand-holding-usd',
                        'pago-integral' => 'fas fa-credit-card',
                        'cajamatic' => 'fas fa-cash-register',
                        'atencion-cliente' => 'fas fa-headset',
                        'informes' => 'fas fa-chart-bar',
                    ];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($grouped as $group => $perms)
                    <div class="border border-slate_custom-200 rounded-xl overflow-hidden">
                        <div class="bg-slate_custom-50 px-4 py-2.5 border-b border-slate_custom-200 flex items-center gap-2">
                            <i class="{{ $groupIcons[$group] ?? 'fas fa-lock' }} text-burgundy-800 text-sm"></i>
                            <span class="text-sm font-heading font-bold text-navy-800 capitalize">{{ str_replace('-', ' ', $group) }}</span>
                            <span class="text-xs text-slate_custom-400 ml-auto">{{ $perms->count() }}</span>
                        </div>
                        <div class="p-3 space-y-1">
                            @foreach($perms as $permission)
                            <label class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-slate_custom-50 cursor-pointer transition">
                                <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                       class="permission-checkbox rounded border-slate_custom-300 text-burgundy-800 focus:ring-burgundy-800"
                                       {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                <span class="text-sm text-navy-800">{{ $permission->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                @error('permissions')
                    <p class="text-red-500 text-xs mt-3">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.roles.index') }}" class="btn-secondary">
                <i class="fas fa-times mr-2"></i>Cancelar
            </a>
            <button type="submit" class="btn-primary">
                <i class="fas fa-save mr-2"></i>{{ isset($role) ? 'Actualizar Rol' : 'Crear Rol' }}
            </button>
        </div>
    </form>

    <script>
        function toggleAll(checked) {
            document.querySelectorAll('.permission-checkbox').forEach(function(cb) {
                cb.checked = checked;
            });
        }
    </script>
</x-app-layout>
