<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">
                    {{ isset($fondo) ? 'Editar Fondo' : 'Nuevo Fondo' }}
                </h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    {{ isset($fondo) ? 'Modificar datos del fondo' : 'Registrar un nuevo fondo financiero' }}
                </p>
            </div>
            <a href="{{ route('financiero.fondos.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    <div class="card max-w-3xl mx-auto">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-piggy-bank mr-2 text-burgundy-800"></i>Datos del Fondo
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ isset($fondo) ? route('financiero.fondos.update', $fondo) : route('financiero.fondos.store') }}" method="POST">
                @csrf
                @if(isset($fondo))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Compania -->
                    <div>
                        <label for="compania_id" class="block text-sm font-medium text-navy-800 mb-1">Compa&ntilde;&iacute;a <span class="text-red-500">*</span></label>
                        <select name="compania_id" id="compania_id" class="form-select w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" required>
                            <option value="">Seleccione una compa&ntilde;&iacute;a</option>
                            @foreach($companias as $compania)
                                <option value="{{ $compania->id }}" {{ old('compania_id', $fondo->compania_id ?? '') == $compania->id ? 'selected' : '' }}>
                                    {{ $compania->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('compania_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nombre -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-navy-800 mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $fondo->nombre ?? '') }}" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" required>
                        @error('nombre')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tipo -->
                    <div>
                        <label for="tipo" class="block text-sm font-medium text-navy-800 mb-1">Tipo <span class="text-red-500">*</span></label>
                        <select name="tipo" id="tipo" class="form-select w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800" required>
                            <option value="">Seleccione un tipo</option>
                            @foreach(['contingencias' => 'Contingencias', 'prestaciones' => 'Prestaciones', 'reserva' => 'Reserva', 'especial' => 'Especial'] as $key => $label)
                                <option value="{{ $key }}" {{ old('tipo', $fondo->tipo ?? '') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Saldo Actual -->
                    <div>
                        <label for="saldo_actual" class="block text-sm font-medium text-navy-800 mb-1">Saldo Actual</label>
                        <input type="number" name="saldo_actual" id="saldo_actual" value="{{ old('saldo_actual', $fondo->saldo_actual ?? '0.00') }}" step="0.01" min="0" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">
                        @error('saldo_actual')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Meta -->
                    <div>
                        <label for="meta" class="block text-sm font-medium text-navy-800 mb-1">Meta</label>
                        <input type="number" name="meta" id="meta" value="{{ old('meta', $fondo->meta ?? '0.00') }}" step="0.01" min="0" class="form-input w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">
                        @error('meta')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Activo -->
                    <div class="flex items-center pt-6">
                        <label class="flex items-center cursor-pointer">
                            <input type="hidden" name="activo" value="0">
                            <input type="checkbox" name="activo" value="1" class="form-checkbox rounded border-slate_custom-300 text-burgundy-800 focus:ring-burgundy-800" {{ old('activo', $fondo->activo ?? true) ? 'checked' : '' }}>
                            <span class="ml-2 text-sm font-medium text-navy-800">Activo</span>
                        </label>
                    </div>
                </div>

                <!-- Descripcion -->
                <div class="mt-6">
                    <label for="descripcion" class="block text-sm font-medium text-navy-800 mb-1">Descripci&oacute;n</label>
                    <textarea name="descripcion" id="descripcion" rows="3" class="form-textarea w-full rounded-lg border-slate_custom-200 focus:border-burgundy-800 focus:ring-burgundy-800">{{ old('descripcion', $fondo->descripcion ?? '') }}</textarea>
                    @error('descripcion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate_custom-100">
                    <a href="{{ route('financiero.fondos.index') }}" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>{{ isset($fondo) ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
