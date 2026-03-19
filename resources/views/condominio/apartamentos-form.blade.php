<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">
                    {{ isset($apartamento) ? 'Editar Apartamento' : 'Crear Apartamento' }}
                </h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    {{ isset($apartamento) ? 'Modificar datos del apartamento' : 'Registrar nuevo apartamento en el sistema' }}
                </p>
            </div>
            <a href="{{ route('condominio.apartamentos.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-door-open mr-2 text-burgundy-800"></i>
                {{ isset($apartamento) ? 'Formulario de Edicion' : 'Formulario de Registro' }}
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ isset($apartamento) ? route('condominio.apartamentos.update', $apartamento) : route('condominio.apartamentos.store') }}" method="POST">
                @csrf
                @if(isset($apartamento))
                    @method('PUT')
                @endif

                {{-- Location Info --}}
                <h4 class="text-sm font-heading font-semibold text-navy-800 mb-4 pb-2 border-b border-slate_custom-200">
                    <i class="fas fa-map-marker-alt mr-2 text-burgundy-800"></i>Ubicacion
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <div>
                        <label for="edificio_id" class="block text-sm font-medium text-navy-800 mb-1">Edificio <span class="text-red-500">*</span></label>
                        <select name="edificio_id" id="edificio_id"
                                class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                                required>
                            <option value="">Seleccione un edificio</option>
                            @foreach($edificios as $edificio)
                                <option value="{{ $edificio->id }}" {{ old('edificio_id', $apartamento->edificio_id ?? '') == $edificio->id ? 'selected' : '' }}>
                                    {{ $edificio->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('edificio_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="num_apto" class="block text-sm font-medium text-navy-800 mb-1">Num. Apartamento <span class="text-red-500">*</span></label>
                        <input type="text" name="num_apto" id="num_apto"
                               value="{{ old('num_apto', $apartamento->num_apto ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               required>
                        @error('num_apto')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="piso" class="block text-sm font-medium text-navy-800 mb-1">Piso</label>
                        <input type="text" name="piso" id="piso"
                               value="{{ old('piso', $apartamento->piso ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                        @error('piso')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Physical Characteristics --}}
                <h4 class="text-sm font-heading font-semibold text-navy-800 mb-4 pb-2 border-b border-slate_custom-200">
                    <i class="fas fa-ruler-combined mr-2 text-burgundy-800"></i>Caracteristicas
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <div>
                        <label for="area_mts" class="block text-sm font-medium text-navy-800 mb-1">Area (m2)</label>
                        <input type="number" name="area_mts" id="area_mts"
                               value="{{ old('area_mts', $apartamento->area_mts ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               step="0.01" min="0">
                        @error('area_mts')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="alicuota" class="block text-sm font-medium text-navy-800 mb-1">Alicuota</label>
                        <input type="number" name="alicuota" id="alicuota"
                               value="{{ old('alicuota', $apartamento->alicuota ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               step="0.0001" min="0">
                        @error('alicuota')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="habitaciones" class="block text-sm font-medium text-navy-800 mb-1">Habitaciones</label>
                        <input type="number" name="habitaciones" id="habitaciones"
                               value="{{ old('habitaciones', $apartamento->habitaciones ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               min="0">
                        @error('habitaciones')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="banos" class="block text-sm font-medium text-navy-800 mb-1">Banos</label>
                        <input type="number" name="banos" id="banos"
                               value="{{ old('banos', $apartamento->banos ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               min="0">
                        @error('banos')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center pt-6">
                        <input type="hidden" name="estacionamiento" value="0">
                        <input type="checkbox" name="estacionamiento" id="estacionamiento" value="1"
                               {{ old('estacionamiento', $apartamento->estacionamiento ?? false) ? 'checked' : '' }}
                               class="rounded border-slate_custom-300 text-burgundy-800 shadow-sm focus:ring-burgundy-800">
                        <label for="estacionamiento" class="ml-2 text-sm font-medium text-navy-800">Tiene Estacionamiento</label>
                    </div>

                    <div>
                        <label for="estatus" class="block text-sm font-medium text-navy-800 mb-1">Estatus <span class="text-red-500">*</span></label>
                        <select name="estatus" id="estatus"
                                class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                                required>
                            <option value="A" {{ old('estatus', $apartamento->estatus ?? 'A') == 'A' ? 'selected' : '' }}>Activo</option>
                            <option value="I" {{ old('estatus', $apartamento->estatus ?? '') == 'I' ? 'selected' : '' }}>Inactivo</option>
                            <option value="M" {{ old('estatus', $apartamento->estatus ?? '') == 'M' ? 'selected' : '' }}>Moroso</option>
                        </select>
                        @error('estatus')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Owner Info --}}
                <h4 class="text-sm font-heading font-semibold text-navy-800 mb-4 pb-2 border-b border-slate_custom-200">
                    <i class="fas fa-user mr-2 text-burgundy-800"></i>Datos del Propietario
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div>
                        <label for="propietario_nombre" class="block text-sm font-medium text-navy-800 mb-1">Nombre del Propietario</label>
                        <input type="text" name="propietario_nombre" id="propietario_nombre"
                               value="{{ old('propietario_nombre', $apartamento->propietario_nombre ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                        @error('propietario_nombre')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="propietario_cedula" class="block text-sm font-medium text-navy-800 mb-1">Cedula del Propietario</label>
                        <input type="text" name="propietario_cedula" id="propietario_cedula"
                               value="{{ old('propietario_cedula', $apartamento->propietario_cedula ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                        @error('propietario_cedula')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="propietario_telefono" class="block text-sm font-medium text-navy-800 mb-1">Telefono del Propietario</label>
                        <input type="text" name="propietario_telefono" id="propietario_telefono"
                               value="{{ old('propietario_telefono', $apartamento->propietario_telefono ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                        @error('propietario_telefono')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="propietario_email" class="block text-sm font-medium text-navy-800 mb-1">Email del Propietario</label>
                        <input type="email" name="propietario_email" id="propietario_email"
                               value="{{ old('propietario_email', $apartamento->propietario_email ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                        @error('propietario_email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate_custom-200">
                    <a href="{{ route('condominio.apartamentos.index') }}" class="btn-secondary">
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
