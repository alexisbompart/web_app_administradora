<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">
                    {{ isset($usuario) ? 'Editar Usuario' : 'Crear Usuario' }}
                </h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    {{ isset($usuario) ? 'Modificar datos del usuario' : 'Registrar nuevo usuario en el sistema' }}
                </p>
            </div>
            <a href="{{ route('admin.usuarios.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    <div x-data="usuarioForm()" class="space-y-6">
        <div class="card">
            <div class="card-header">
                <h3 class="text-sm font-heading font-semibold text-navy-800">
                    <i class="fas fa-user-cog mr-2 text-burgundy-800"></i>
                    {{ isset($usuario) ? 'Formulario de Edicion' : 'Formulario de Registro' }}
                </h3>
            </div>
            <div class="card-body">
                <form action="{{ isset($usuario) ? route('admin.usuarios.update', $usuario) : route('admin.usuarios.store') }}" method="POST">
                    @csrf
                    @if(isset($usuario))
                        @method('PUT')
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-navy-800 mb-1">Nombre <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name"
                                   value="{{ old('name', $usuario->name ?? '') }}"
                                   class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                                   required>
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-navy-800 mb-1">Email <span class="text-red-500">*</span></label>
                            <input type="email" name="email" id="email"
                                   value="{{ old('email', $usuario->email ?? '') }}"
                                   class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                                   required>
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="cedula" class="block text-sm font-medium text-navy-800 mb-1">Cedula</label>
                            <input type="text" name="cedula" id="cedula"
                                   value="{{ old('cedula', $usuario->cedula ?? '') }}"
                                   class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                            @error('cedula')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="telefono" class="block text-sm font-medium text-navy-800 mb-1">Telefono</label>
                            <input type="text" name="telefono" id="telefono"
                                   value="{{ old('telefono', $usuario->telefono ?? '') }}"
                                   class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                            @error('telefono')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-navy-800 mb-1">
                                Contrasena {{ isset($usuario) ? '(dejar vacio para mantener)' : '' }} <span class="{{ isset($usuario) ? '' : 'text-red-500' }}">{{ isset($usuario) ? '' : '*' }}</span>
                            </label>
                            <input type="password" name="password" id="password"
                                   class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                                   {{ isset($usuario) ? '' : 'required' }}>
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-navy-800 mb-1">
                                Confirmar Contrasena {{ isset($usuario) ? '' : '*' }}
                            </label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                                   {{ isset($usuario) ? '' : 'required' }}>
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-navy-800 mb-1">Rol <span class="text-red-500">*</span></label>
                            <select name="role" id="role" x-model="selectedRole"
                                    class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                                    required>
                                <option value="">Seleccione un rol</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}"
                                        {{ old('role', isset($usuario) && $usuario->roles->first() ? $usuario->roles->first()->name : '') == $role->name ? 'selected' : '' }}>
                                        {{ ucfirst($role->name) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('role')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            <p x-show="selectedRole === 'cliente-propietario'" x-cloak class="text-xs text-blue-600 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>Se creara automaticamente el perfil de propietario al guardar.
                            </p>
                        </div>

                        <div>
                            <label for="activo" class="block text-sm font-medium text-navy-800 mb-1">Estatus</label>
                            <select name="activo" id="activo"
                                    class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                                <option value="1" {{ old('activo', $usuario->activo ?? true) ? 'selected' : '' }}>Activo</option>
                                <option value="0" {{ !old('activo', $usuario->activo ?? true) ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>
                    </div>

                    {{-- Seccion Apartamento - solo visible para cliente-propietario --}}
                    <div x-show="selectedRole === 'cliente-propietario'" x-cloak
                         class="mt-6 pt-6 border-t border-slate_custom-200">
                        <h4 class="text-sm font-heading font-semibold text-navy-800 mb-4">
                            <i class="fas fa-building mr-2 text-burgundy-800"></i>Asignar Apartamento
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-navy-800 mb-1">Edificio</label>
                                <select x-model="edificioId" @change="cargarApartamentos()"
                                        class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                                    <option value="">-- Seleccionar edificio --</option>
                                    @foreach($edificios as $edif)
                                        <option value="{{ $edif->id }}"
                                            {{ (isset($apartamentoActual) && $apartamentoActual->edificio_id == $edif->id) ? 'selected' : '' }}>
                                            {{ $edif->nombre ?? $edif->cod_edif }} ({{ $edif->cod_edif }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-navy-800 mb-1">Apartamento</label>
                                <select name="apartamento_id" x-model="apartamentoId"
                                        :disabled="!apartamentos.length"
                                        class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                                    <option value="">-- Seleccionar apartamento --</option>
                                    <template x-for="apto in apartamentos" :key="apto.id">
                                        <option :value="apto.id" x-text="apto.num_apto"
                                                :selected="apto.id == apartamentoId"></option>
                                    </template>
                                </select>
                                @error('apartamento_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <p class="text-xs text-slate_custom-400 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>Seleccione el edificio y apartamento del propietario para que pueda acceder al portal.
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-slate_custom-200">
                        <a href="{{ route('admin.usuarios.index') }}" class="btn-secondary">
                            <i class="fas fa-times mr-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save mr-2"></i>Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    function usuarioForm() {
        return {
            selectedRole: '{{ old('role', isset($usuario) && $usuario->roles->first() ? $usuario->roles->first()->name : '') }}',
            edificioId: '{{ isset($apartamentoActual) ? $apartamentoActual->edificio_id : '' }}',
            apartamentoId: '{{ old('apartamento_id', isset($apartamentoActual) ? $apartamentoActual->id : '') }}',
            apartamentos: [],
            init() {
                if (this.edificioId) {
                    this.cargarApartamentos();
                }
            },
            cargarApartamentos() {
                this.apartamentos = [];
                if (!this.edificioId) {
                    this.apartamentoId = '';
                    return;
                }
                var self = this;
                var savedAptoId = this.apartamentoId;
                fetch('/admin/apartamentos-por-edificio/' + this.edificioId)
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        self.apartamentos = data;
                        // Restore selection if editing
                        var found = data.find(function(a) { return a.id == savedAptoId; });
                        if (!found) self.apartamentoId = '';
                    });
            }
        }
    }
    </script>
</x-app-layout>
