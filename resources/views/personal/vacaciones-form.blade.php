<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">
                    {{ isset($vacacion) ? 'Editar Vacaci&oacute;n' : 'Crear Vacaci&oacute;n' }}
                </h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    {{ isset($vacacion) ? 'Modificar registro de vacaciones' : 'Registrar nuevas vacaciones' }}
                </p>
            </div>
            <a href="{{ route('personal.vacaciones.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-umbrella-beach mr-2 text-burgundy-800"></i>Datos de Vacaciones
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

            <form action="{{ isset($vacacion) ? route('personal.vacaciones.update', $vacacion) : route('personal.vacaciones.store') }}" method="POST">
                @csrf
                @if(isset($vacacion))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {{-- Trabajador --}}
                    <div>
                        <label for="trabajador_id" class="block text-sm font-medium text-navy-800 mb-1">Trabajador <span class="text-red-500">*</span></label>
                        <select name="trabajador_id" id="trabajador_id" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm" required>
                            <option value="">Seleccione...</option>
                            @foreach($trabajadores as $trabajador)
                                <option value="{{ $trabajador->id }}" {{ old('trabajador_id', $vacacion->trabajador_id ?? '') == $trabajador->id ? 'selected' : '' }}>
                                    {{ $trabajador->cedula }} - {{ $trabajador->nombre_completo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Periodo Desde --}}
                    <div>
                        <label for="periodo_desde" class="block text-sm font-medium text-navy-800 mb-1">Periodo Desde <span class="text-red-500">*</span></label>
                        <input type="date" name="periodo_desde" id="periodo_desde" value="{{ old('periodo_desde', isset($vacacion) && $vacacion->periodo_desde ? $vacacion->periodo_desde->format('Y-m-d') : '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm" required>
                    </div>

                    {{-- Periodo Hasta --}}
                    <div>
                        <label for="periodo_hasta" class="block text-sm font-medium text-navy-800 mb-1">Periodo Hasta <span class="text-red-500">*</span></label>
                        <input type="date" name="periodo_hasta" id="periodo_hasta" value="{{ old('periodo_hasta', isset($vacacion) && $vacacion->periodo_hasta ? $vacacion->periodo_hasta->format('Y-m-d') : '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm" required>
                    </div>

                    {{-- Dias Correspondientes --}}
                    <div>
                        <label for="dias_correspondientes" class="block text-sm font-medium text-navy-800 mb-1">D&iacute;as Correspondientes <span class="text-red-500">*</span></label>
                        <input type="number" name="dias_correspondientes" id="dias_correspondientes" value="{{ old('dias_correspondientes', $vacacion->dias_correspondientes ?? '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm" min="0" required>
                    </div>

                    {{-- Dias Disfrutados --}}
                    <div>
                        <label for="dias_disfrutados" class="block text-sm font-medium text-navy-800 mb-1">D&iacute;as Disfrutados</label>
                        <input type="number" name="dias_disfrutados" id="dias_disfrutados" value="{{ old('dias_disfrutados', $vacacion->dias_disfrutados ?? 0) }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm" min="0">
                    </div>

                    {{-- Fecha Salida --}}
                    <div>
                        <label for="fecha_salida" class="block text-sm font-medium text-navy-800 mb-1">Fecha de Salida</label>
                        <input type="date" name="fecha_salida" id="fecha_salida" value="{{ old('fecha_salida', isset($vacacion) && $vacacion->fecha_salida ? $vacacion->fecha_salida->format('Y-m-d') : '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                    </div>

                    {{-- Fecha Reincorporacion --}}
                    <div>
                        <label for="fecha_reincorporacion" class="block text-sm font-medium text-navy-800 mb-1">Fecha de Reincorporaci&oacute;n</label>
                        <input type="date" name="fecha_reincorporacion" id="fecha_reincorporacion" value="{{ old('fecha_reincorporacion', isset($vacacion) && $vacacion->fecha_reincorporacion ? $vacacion->fecha_reincorporacion->format('Y-m-d') : '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                    </div>

                    {{-- Suplente --}}
                    <div>
                        <label for="suplente_id" class="block text-sm font-medium text-navy-800 mb-1">Suplente</label>
                        <select name="suplente_id" id="suplente_id" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                            <option value="">Seleccione...</option>
                            @foreach($trabajadores as $trabajador)
                                <option value="{{ $trabajador->id }}" {{ old('suplente_id', $vacacion->suplente_id ?? '') == $trabajador->id ? 'selected' : '' }}>
                                    {{ $trabajador->cedula }} - {{ $trabajador->nombre_completo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Monto Bono Vacacional --}}
                    <div>
                        <label for="monto_bono_vacacional" class="block text-sm font-medium text-navy-800 mb-1">Monto Bono Vacacional</label>
                        <input type="number" step="0.01" name="monto_bono_vacacional" id="monto_bono_vacacional" value="{{ old('monto_bono_vacacional', $vacacion->monto_bono_vacacional ?? '') }}" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm" min="0">
                    </div>

                    {{-- Estatus --}}
                    <div>
                        <label for="estatus" class="block text-sm font-medium text-navy-800 mb-1">Estatus</label>
                        <select name="estatus" id="estatus" class="w-full rounded-lg border-slate_custom-300 focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                            <option value="pendiente" {{ old('estatus', $vacacion->estatus ?? 'pendiente') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                            <option value="aprobada" {{ old('estatus', $vacacion->estatus ?? '') == 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                            <option value="rechazada" {{ old('estatus', $vacacion->estatus ?? '') == 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                            <option value="disfrutada" {{ old('estatus', $vacacion->estatus ?? '') == 'disfrutada' ? 'selected' : '' }}>Disfrutada</option>
                        </select>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-slate_custom-200">
                    <a href="{{ route('personal.vacaciones.index') }}" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>{{ isset($vacacion) ? 'Actualizar' : 'Guardar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
