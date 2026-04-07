<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">{{ isset($afilapto) ? 'Editar' : 'Crear' }} Afiliacion</h2>
                <p class="text-sm text-slate_custom-400 mt-1">{{ isset($afilapto) ? 'Modificar datos de la afiliacion #'.$afilapto->id : 'Registrar nueva afiliacion de apartamento' }}</p>
            </div>
            <a href="{{ route('condominio.afilapto.index') }}" class="btn-secondary"><i class="fas fa-arrow-left mr-2"></i>Volver</a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800"><i class="fas fa-link mr-2 text-burgundy-800"></i>Datos de la Afiliacion</h3>
        </div>
        <div class="card-body"
             x-data="{
                edificioId: '{{ old('edificio_id', $afilapto->edificio_id ?? '') }}',
                apartamentoId: '{{ old('apartamento_id', $afilapto->apartamento_id ?? '') }}',
                apartamentos: @js(isset($apartamentos) ? $apartamentos->map(fn($a) => ['id' => $a->id, 'num_apto' => $a->num_apto]) : []),
                loading: false,
                fetchApartamentos() {
                    if (!this.edificioId) { this.apartamentos = []; this.apartamentoId = ''; return; }
                    this.loading = true;
                    fetch('/admin/apartamentos-por-edificio/' + this.edificioId)
                        .then(r => r.json())
                        .then(data => { this.apartamentos = data; this.loading = false; })
                        .catch(() => { this.apartamentos = []; this.loading = false; });
                }
             }">
            <form action="{{ isset($afilapto) ? route('condominio.afilapto.update', $afilapto) : route('condominio.afilapto.store') }}" method="POST">
                @csrf
                @if(isset($afilapto)) @method('PUT') @endif

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Edificio</label>
                        <select name="edificio_id" x-model="edificioId" @change="apartamentoId = ''; fetchApartamentos()" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                            <option value="">-- Seleccionar --</option>
                            @foreach($edificios as $edif)
                                <option value="{{ $edif->id }}">
                                    {{ $edif->nombre ?? $edif->cod_edif }} ({{ $edif->cod_edif }})
                                </option>
                            @endforeach
                        </select>
                        @error('edificio_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Apartamento</label>
                        <select name="apartamento_id" x-model="apartamentoId" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                            <option value="">-- Seleccionar --</option>
                            <template x-for="apto in apartamentos" :key="apto.id">
                                <option :value="apto.id" x-text="apto.num_apto"></option>
                            </template>
                        </select>
                        <p x-show="loading" class="text-xs text-slate_custom-400 mt-1"><i class="fas fa-spinner fa-spin mr-1"></i>Cargando apartamentos...</p>
                        @error('apartamento_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Compania</label>
                        <select name="compania_id" class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                            <option value="">-- Seleccionar --</option>
                            @foreach($companias as $comp)
                                <option value="{{ $comp->id }}" {{ old('compania_id', $afilapto->compania_id ?? '') == $comp->id ? 'selected' : '' }}>
                                    {{ $comp->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('compania_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Estatus <span class="text-red-500">*</span></label>
                        <select name="estatus_afil" required class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                            <option value="A" {{ old('estatus_afil', $afilapto->estatus_afil ?? '') === 'A' ? 'selected' : '' }}>Activo</option>
                            <option value="D" {{ old('estatus_afil', $afilapto->estatus_afil ?? '') === 'D' ? 'selected' : '' }}>Desactivado</option>
                            <option value="P" {{ old('estatus_afil', $afilapto->estatus_afil ?? '') === 'P' ? 'selected' : '' }}>Pendiente</option>
                        </select>
                        @error('estatus_afil')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Fecha Afiliacion</label>
                        <input type="date" name="fecha_afiliacion" value="{{ old('fecha_afiliacion', isset($afilapto) && $afilapto->fecha_afiliacion ? $afilapto->fecha_afiliacion->format('Y-m-d') : '') }}"
                               class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                        @error('fecha_afiliacion')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-navy-800 mb-1">Observaciones</label>
                        <input type="text" name="observaciones" value="{{ old('observaciones', $afilapto->observaciones ?? '') }}" maxlength="500"
                               class="w-full rounded-lg border-slate_custom-200 text-sm focus:border-burgundy-800 focus:ring-burgundy-800">
                        @error('observaciones')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate_custom-200">
                    <a href="{{ route('condominio.afilapto.index') }}" class="btn-secondary"><i class="fas fa-times mr-2"></i>Cancelar</a>
                    <button type="submit" class="btn-primary"><i class="fas fa-save mr-2"></i>{{ isset($afilapto) ? 'Actualizar' : 'Crear' }}</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
