<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">{{ isset($tasa) ? 'Editar' : 'Registrar' }} Tasa BCV</h2>
                <p class="text-sm text-slate_custom-400 mt-1">{{ isset($tasa) ? 'Modificar tasa del '.$tasa->fecha->format('d/m/Y') : 'Nueva tasa del Banco Central de Venezuela' }}</p>
            </div>
            <a href="{{ route('financiero.tasabcv.index') }}" class="btn-secondary"><i class="fas fa-arrow-left mr-2"></i>Volver</a>
        </div>
    </x-slot>

    <div class="card max-w-lg">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800"><i class="fas fa-dollar-sign mr-2 text-burgundy-800"></i>Datos de la Tasa</h3>
        </div>
        <div class="card-body">
            <form action="{{ isset($tasa) ? route('financiero.tasabcv.update', $tasa) : route('financiero.tasabcv.store') }}" method="POST">
                @csrf
                @if(isset($tasa)) @method('PUT') @endif

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Fecha <span class="text-red-500">*</span></label>
                        <input type="date" name="fecha" value="{{ old('fecha', isset($tasa) ? $tasa->fecha->format('Y-m-d') : now()->format('Y-m-d')) }}" required
                               class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                        @error('fecha')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Tasa (Bs/USD) <span class="text-red-500">*</span></label>
                        <input type="number" name="tasa" step="0.0001" min="0.0001" value="{{ old('tasa', $tasa->tasa ?? '') }}" required
                               class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800"
                               placeholder="Ej: 466.60">
                        @error('tasa')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-slate_custom-200">
                    <a href="{{ route('financiero.tasabcv.index') }}" class="btn-secondary"><i class="fas fa-times mr-2"></i>Cancelar</a>
                    <button type="submit" class="btn-primary"><i class="fas fa-save mr-2"></i>{{ isset($tasa) ? 'Actualizar' : 'Guardar' }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
