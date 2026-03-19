<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">
                    {{ isset($propietario) ? 'Editar Propietario' : 'Crear Propietario' }}
                </h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    {{ isset($propietario) ? 'Modificar datos del propietario' : 'Registrar nuevo propietario en el sistema' }}
                </p>
            </div>
            <a href="{{ route('condominio.propietarios.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-user mr-2 text-burgundy-800"></i>
                {{ isset($propietario) ? 'Formulario de Edicion' : 'Formulario de Registro' }}
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ isset($propietario) ? route('condominio.propietarios.update', $propietario) : route('condominio.propietarios.store') }}" method="POST">
                @csrf
                @if(isset($propietario))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="cedula" class="block text-sm font-medium text-navy-800 mb-1">Cedula <span class="text-red-500">*</span></label>
                        <input type="text" name="cedula" id="cedula"
                               value="{{ old('cedula', $propietario->cedula ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               required>
                        @error('cedula')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nombres" class="block text-sm font-medium text-navy-800 mb-1">Nombres <span class="text-red-500">*</span></label>
                        <input type="text" name="nombres" id="nombres"
                               value="{{ old('nombres', $propietario->nombres ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               required>
                        @error('nombres')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="apellidos" class="block text-sm font-medium text-navy-800 mb-1">Apellidos <span class="text-red-500">*</span></label>
                        <input type="text" name="apellidos" id="apellidos"
                               value="{{ old('apellidos', $propietario->apellidos ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               required>
                        @error('apellidos')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telefono" class="block text-sm font-medium text-navy-800 mb-1">Telefono</label>
                        <input type="text" name="telefono" id="telefono"
                               value="{{ old('telefono', $propietario->telefono ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                        @error('telefono')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="celular" class="block text-sm font-medium text-navy-800 mb-1">Celular</label>
                        <input type="text" name="celular" id="celular"
                               value="{{ old('celular', $propietario->celular ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                        @error('celular')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-navy-800 mb-1">Email</label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email', $propietario->email ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="estatus" class="block text-sm font-medium text-navy-800 mb-1">Estatus</label>
                        <select name="estatus" id="estatus"
                                class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                            <option value="1" {{ old('estatus', $propietario->estatus ?? true) ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ !old('estatus', $propietario->estatus ?? true) ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('estatus')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="direccion" class="block text-sm font-medium text-navy-800 mb-1">Direccion</label>
                        <textarea name="direccion" id="direccion" rows="3"
                                  class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">{{ old('direccion', $propietario->direccion ?? '') }}</textarea>
                        @error('direccion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-slate_custom-200">
                    <a href="{{ route('condominio.propietarios.index') }}" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
