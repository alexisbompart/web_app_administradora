<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">
                    {{ isset($solicitud) ? 'Editar Solicitud' : 'Nueva Solicitud' }}
                </h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    {{ isset($solicitud) ? 'Modificar datos de la solicitud #' . $solicitud->id : 'Registrar nueva solicitud de atencion al cliente' }}
                </p>
            </div>
            <a href="{{ route('servicios.atencion-cliente.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-headset mr-2 text-burgundy-800"></i>
                {{ isset($solicitud) ? 'Formulario de Edicion' : 'Formulario de Registro' }}
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ isset($solicitud) ? route('servicios.atencion-cliente.update', $solicitud) : route('servicios.atencion-cliente.store') }}" method="POST">
                @csrf
                @if(isset($solicitud))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="tipo" class="block text-sm font-medium text-navy-800 mb-1">Tipo de Solicitud <span class="text-red-500">*</span></label>
                        <select name="tipo" id="tipo"
                                class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                                required>
                            <option value="">Seleccione tipo</option>
                            <option value="consulta" {{ old('tipo', $solicitud->tipo ?? '') == 'consulta' ? 'selected' : '' }}>Consulta</option>
                            <option value="queja" {{ old('tipo', $solicitud->tipo ?? '') == 'queja' ? 'selected' : '' }}>Queja</option>
                            <option value="solicitud" {{ old('tipo', $solicitud->tipo ?? '') == 'solicitud' ? 'selected' : '' }}>Solicitud</option>
                            <option value="emergencia" {{ old('tipo', $solicitud->tipo ?? '') == 'emergencia' ? 'selected' : '' }}>Emergencia</option>
                            <option value="asesoria_legal" {{ old('tipo', $solicitud->tipo ?? '') == 'asesoria_legal' ? 'selected' : '' }}>Asesoria Legal</option>
                            <option value="asamblea" {{ old('tipo', $solicitud->tipo ?? '') == 'asamblea' ? 'selected' : '' }}>Asamblea</option>
                        </select>
                        @error('tipo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="prioridad" class="block text-sm font-medium text-navy-800 mb-1">Prioridad <span class="text-red-500">*</span></label>
                        <select name="prioridad" id="prioridad"
                                class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                                required>
                            <option value="baja" {{ old('prioridad', $solicitud->prioridad ?? 'media') == 'baja' ? 'selected' : '' }}>Baja</option>
                            <option value="media" {{ old('prioridad', $solicitud->prioridad ?? 'media') == 'media' ? 'selected' : '' }}>Media</option>
                            <option value="alta" {{ old('prioridad', $solicitud->prioridad ?? 'media') == 'alta' ? 'selected' : '' }}>Alta</option>
                            <option value="urgente" {{ old('prioridad', $solicitud->prioridad ?? 'media') == 'urgente' ? 'selected' : '' }}>Urgente</option>
                        </select>
                        @error('prioridad')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="edificio_id" class="block text-sm font-medium text-navy-800 mb-1">Edificio</label>
                        <select name="edificio_id" id="edificio_id"
                                class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                            <option value="">-- Sin edificio --</option>
                            @foreach($edificios as $edificio)
                                <option value="{{ $edificio->id }}" {{ old('edificio_id', $solicitud->edificio_id ?? '') == $edificio->id ? 'selected' : '' }}>
                                    {{ $edificio->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('edificio_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="propietario_id" class="block text-sm font-medium text-navy-800 mb-1">Propietario</label>
                        <select name="propietario_id" id="propietario_id"
                                class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                            <option value="">-- Sin propietario --</option>
                            @foreach($propietarios as $prop)
                                <option value="{{ $prop->id }}" {{ old('propietario_id', $solicitud->propietario_id ?? '') == $prop->id ? 'selected' : '' }}>
                                    {{ $prop->cedula }} - {{ $prop->nombres }} {{ $prop->apellidos }}
                                </option>
                            @endforeach
                        </select>
                        @error('propietario_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="asunto" class="block text-sm font-medium text-navy-800 mb-1">Asunto <span class="text-red-500">*</span></label>
                        <input type="text" name="asunto" id="asunto"
                               value="{{ old('asunto', $solicitud->asunto ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               placeholder="Describa brevemente el asunto"
                               maxlength="200"
                               required>
                        @error('asunto')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="descripcion" class="block text-sm font-medium text-navy-800 mb-1">Descripcion</label>
                        <textarea name="descripcion" id="descripcion" rows="4"
                                  class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                                  placeholder="Detalle de la solicitud...">{{ old('descripcion', $solicitud->descripcion ?? '') }}</textarea>
                        @error('descripcion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @if(isset($solicitud))
                        <div>
                            <label for="estatus" class="block text-sm font-medium text-navy-800 mb-1">Estatus <span class="text-red-500">*</span></label>
                            <select name="estatus" id="estatus"
                                    class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                                    required>
                                <option value="abierto" {{ old('estatus', $solicitud->estatus) == 'abierto' ? 'selected' : '' }}>Abierto</option>
                                <option value="en_proceso" {{ old('estatus', $solicitud->estatus) == 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                                <option value="resuelto" {{ old('estatus', $solicitud->estatus) == 'resuelto' ? 'selected' : '' }}>Resuelto</option>
                                <option value="cerrado" {{ old('estatus', $solicitud->estatus) == 'cerrado' ? 'selected' : '' }}>Cerrado</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="respuesta" class="block text-sm font-medium text-navy-800 mb-1">Respuesta / Resolucion</label>
                            <textarea name="respuesta" id="respuesta" rows="3"
                                      class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                                      placeholder="Respuesta o resolucion de la solicitud...">{{ old('respuesta', $solicitud->respuesta ?? '') }}</textarea>
                            @error('respuesta')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-slate_custom-200">
                    <a href="{{ route('servicios.atencion-cliente.index') }}" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>Cancelar
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>{{ isset($solicitud) ? 'Actualizar' : 'Crear Solicitud' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
