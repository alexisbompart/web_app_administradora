<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">
                    {{ isset($nomina) ? 'Editar N&oacute;mina' : 'Crear N&oacute;mina' }}
                </h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    {{ isset($nomina) ? 'Modificar datos de la n&oacute;mina' : 'Registrar una nueva n&oacute;mina' }}
                </p>
            </div>
            <a href="{{ route('personal.nominas.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-money-check-alt mr-2 text-burgundy-800"></i>Datos de la N&oacute;mina
            </h3>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ isset($nomina) ? route('personal.nominas.update', $nomina) : route('personal.nominas.store') }}" method="POST">
                @csrf
                @if(isset($nomina))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Compania --}}
                    <div>
                        <label for="compania_id" class="block text-sm font-medium text-navy-800 mb-1">Compa&ntilde;&iacute;a <span class="text-red-500">*</span></label>
                        <select name="compania_id" id="compania_id" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm" required>
                            <option value="">Seleccione...</option>
                            @foreach($companias as $compania)
                                <option value="{{ $compania->id }}" {{ old('compania_id', $nomina->compania_id ?? '') == $compania->id ? 'selected' : '' }}>
                                    {{ $compania->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Codigo --}}
                    <div>
                        <label for="codigo" class="block text-sm font-medium text-navy-800 mb-1">C&oacute;digo <span class="text-red-500">*</span></label>
                        <input type="text" name="codigo" id="codigo" value="{{ old('codigo', $nomina->codigo ?? '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm" placeholder="NOM-2026-001" required>
                    </div>

                    {{-- Tipo --}}
                    <div>
                        <label for="tipo" class="block text-sm font-medium text-navy-800 mb-1">Tipo <span class="text-red-500">*</span></label>
                        <select name="tipo" id="tipo" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm" required>
                            <option value="">Seleccione...</option>
                            <option value="quincenal" {{ old('tipo', $nomina->tipo ?? '') == 'quincenal' ? 'selected' : '' }}>Quincenal</option>
                            <option value="mensual" {{ old('tipo', $nomina->tipo ?? '') == 'mensual' ? 'selected' : '' }}>Mensual</option>
                            <option value="especial" {{ old('tipo', $nomina->tipo ?? '') == 'especial' ? 'selected' : '' }}>Especial</option>
                        </select>
                    </div>

                    {{-- Periodo Inicio --}}
                    <div>
                        <label for="periodo_inicio" class="block text-sm font-medium text-navy-800 mb-1">Periodo Inicio <span class="text-red-500">*</span></label>
                        <input type="date" name="periodo_inicio" id="periodo_inicio" value="{{ old('periodo_inicio', isset($nomina) && $nomina->periodo_inicio ? $nomina->periodo_inicio->format('Y-m-d') : '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm" required>
                    </div>

                    {{-- Periodo Fin --}}
                    <div>
                        <label for="periodo_fin" class="block text-sm font-medium text-navy-800 mb-1">Periodo Fin <span class="text-red-500">*</span></label>
                        <input type="date" name="periodo_fin" id="periodo_fin" value="{{ old('periodo_fin', isset($nomina) && $nomina->periodo_fin ? $nomina->periodo_fin->format('Y-m-d') : '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm" required>
                    </div>
                </div>

                {{-- Observaciones --}}
                <div class="mt-6">
                    <label for="observaciones" class="block text-sm font-medium text-navy-800 mb-1">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="3" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">{{ old('observaciones', $nomina->observaciones ?? '') }}</textarea>
                </div>

                {{-- Botones --}}
                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-slate_custom-200">
                    <a href="{{ route('personal.nominas.index') }}" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>{{ isset($nomina) ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
