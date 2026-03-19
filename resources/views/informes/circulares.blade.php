<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">Circulares</h2>
                <p class="text-sm text-slate_custom-400 mt-1">Comunicaciones a la comunidad</p>
            </div>
            <a href="{{ route('servicios.informes.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver a Informes
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Formulario de Nueva Circular -->
    <div class="card mb-6">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-plus-circle mr-2 text-burgundy-800"></i>Nueva Circular
            </h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('servicios.informes.circulares.enviar') }}">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="edificio_id" class="block text-sm font-medium text-navy-800 mb-1">Edificio <span class="text-red-500">*</span></label>
                        <select name="edificio_id" id="edificio_id"
                                class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                                required>
                            <option value="">-- Seleccione Edificio --</option>
                            @foreach($edificios as $edificio)
                                <option value="{{ $edificio->id }}" {{ old('edificio_id') == $edificio->id ? 'selected' : '' }}>
                                    {{ $edificio->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('edificio_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="titulo" class="block text-sm font-medium text-navy-800 mb-1">Titulo <span class="text-red-500">*</span></label>
                        <input type="text" name="titulo" id="titulo"
                               value="{{ old('titulo') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               placeholder="Asunto de la circular"
                               required>
                        @error('titulo')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="descripcion" class="block text-sm font-medium text-navy-800 mb-1">Contenido <span class="text-red-500">*</span></label>
                        <textarea name="descripcion" id="descripcion" rows="6"
                                  class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                                  placeholder="Escriba el contenido de la circular..."
                                  required>{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-slate_custom-200">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-paper-plane mr-2"></i>Publicar Circular
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Listado de Circulares -->
    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-list mr-2 text-burgundy-800"></i>Circulares Publicadas
            </h3>
        </div>
        <div class="card-body p-0">
            @if($circulares->count())
                <div class="divide-y divide-slate_custom-200">
                    @foreach($circulares as $circular)
                        <div class="p-4 hover:bg-slate_custom-50 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-1">
                                        <h4 class="text-sm font-heading font-semibold text-navy-800">{{ $circular->titulo }}</h4>
                                        <span class="badge-success text-xs">Publicada</span>
                                    </div>
                                    <p class="text-sm text-slate_custom-500 mb-2">{{ Str::limit($circular->contenido, 200) }}</p>
                                    <div class="flex items-center gap-4 text-xs text-slate_custom-400">
                                        <span>
                                            <i class="fas fa-building mr-1"></i>{{ $circular->edificio->nombre ?? 'N/A' }}
                                        </span>
                                        <span>
                                            <i class="fas fa-calendar mr-1"></i>{{ $circular->fecha_generacion?->format('d/m/Y') ?? $circular->created_at->format('d/m/Y H:i') }}
                                        </span>
                                        <span>
                                            <i class="fas fa-user mr-1"></i>{{ $circular->usuario->name ?? 'Sistema' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="p-4">
                    {{ $circulares->links() }}
                </div>
            @else
                <div class="p-8 text-center">
                    <div class="w-16 h-16 bg-slate_custom-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-envelope text-2xl text-slate_custom-400"></i>
                    </div>
                    <h3 class="text-lg font-heading font-semibold text-navy-800 mb-2">No hay circulares publicadas</h3>
                    <p class="text-sm text-slate_custom-400">Use el formulario de arriba para publicar la primera circular.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
