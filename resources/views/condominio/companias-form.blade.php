<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">
                    {{ isset($compania) ? 'Editar Compania' : 'Crear Compania' }}
                </h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    {{ isset($compania) ? 'Modificar datos de la compania' : 'Registrar nueva compania en el sistema' }}
                </p>
            </div>
            <a href="{{ route('condominio.companias.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-building mr-2 text-burgundy-800"></i>
                {{ isset($compania) ? 'Formulario de Edicion' : 'Formulario de Registro' }}
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ isset($compania) ? route('condominio.companias.update', $compania) : route('condominio.companias.store') }}" method="POST">
                @csrf
                @if(isset($compania))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Codigo --}}
                    <div>
                        <label for="cod_compania" class="block text-sm font-medium text-navy-800 mb-1">Codigo <span class="text-red-500">*</span></label>
                        <input type="text" name="cod_compania" id="cod_compania"
                               value="{{ old('cod_compania', $compania->cod_compania ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               required>
                        @error('cod_compania')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nombre --}}
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-navy-800 mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre" id="nombre"
                               value="{{ old('nombre', $compania->nombre ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               required>
                        @error('nombre')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- RIF --}}
                    <div>
                        <label for="rif" class="block text-sm font-medium text-navy-800 mb-1">RIF <span class="text-red-500">*</span></label>
                        <input type="text" name="rif" id="rif"
                               value="{{ old('rif', $compania->rif ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               placeholder="J-12345678-9"
                               required>
                        @error('rif')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Telefono --}}
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-navy-800 mb-1">Telefono</label>
                        <input type="text" name="telefono" id="telefono"
                               value="{{ old('telefono', $compania->telefono ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                        @error('telefono')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-navy-800 mb-1">Email</label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email', $compania->email ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Activo --}}
                    <div class="flex items-center pt-6">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" name="activo" id="activo" value="1"
                               {{ old('activo', $compania->activo ?? true) ? 'checked' : '' }}
                               class="rounded border-slate_custom-300 text-burgundy-800 shadow-sm focus:ring-burgundy-800">
                        <label for="activo" class="ml-2 text-sm font-medium text-navy-800">Activo</label>
                    </div>

                    {{-- Direccion (full width) --}}
                    <div class="md:col-span-2">
                        <label for="direccion" class="block text-sm font-medium text-navy-800 mb-1">Direccion</label>
                        <textarea name="direccion" id="direccion" rows="3"
                                  class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">{{ old('direccion', $compania->direccion ?? '') }}</textarea>
                        @error('direccion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Ubicacion en el Mapa --}}
                <div class="mt-8 mb-6">
                    <h4 class="text-sm font-heading font-semibold text-navy-800 mb-4 pb-2 border-b border-slate_custom-200">
                        <i class="fas fa-map-marker-alt mr-2 text-burgundy-800"></i>Ubicacion en el Mapa
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                        <div>
                            <label for="latitud" class="block text-sm font-medium text-navy-800 mb-1">Latitud</label>
                            <input type="number" name="latitud" id="latitud"
                                   value="{{ old('latitud', $compania->latitud ?? '') }}"
                                   class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                                   step="0.0000001" min="-90" max="90" placeholder="Ej: 10.4806">
                            @error('latitud')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="longitud" class="block text-sm font-medium text-navy-800 mb-1">Longitud</label>
                            <input type="number" name="longitud" id="longitud"
                                   value="{{ old('longitud', $compania->longitud ?? '') }}"
                                   class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                                   step="0.0000001" min="-180" max="180" placeholder="Ej: -66.9036">
                            @error('longitud')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-navy-800 mb-1">Buscar direccion</label>
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <input type="text" id="buscar-direccion" placeholder="Escriba una direccion, ciudad o lugar..."
                                       class="w-full pl-10 pr-4 py-2.5 rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate_custom-400 text-sm"></i>
                            </div>
                            <button type="button" id="btn-buscar-dir" class="btn-primary text-sm px-4">
                                <i class="fas fa-map-marker-alt mr-1"></i>Ubicar
                            </button>
                        </div>
                        <div id="resultados-direccion" style="display:none;" class="mt-1 bg-white border border-slate_custom-200 rounded-lg shadow-lg max-h-48 overflow-y-auto z-50 relative"></div>
                        <p class="text-xs text-slate_custom-400 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>Busque la direccion o haga clic en el mapa para seleccionar la ubicacion
                        </p>
                    </div>
                    <div class="rounded-xl overflow-hidden border border-slate_custom-200 shadow-sm" style="height: 350px;">
                        <div id="mapa-compania" style="width: 100%; height: 100%;"></div>
                    </div>
                </div>

                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var latInput = document.getElementById('latitud');
                    var lngInput = document.getElementById('longitud');
                    var lat = parseFloat(latInput.value) || 8.0;
                    var lng = parseFloat(lngInput.value) || -66.0;
                    var zoom = latInput.value ? 15 : 7;

                    var map = L.map('mapa-compania').setView([lat, lng], zoom);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap', maxZoom: 18
                    }).addTo(map);

                    var iconoPin = L.divIcon({
                        html: '<div style="position:relative; width:40px; height:52px;">' +
                              '<svg viewBox="0 0 40 52" width="40" height="52" style="filter: drop-shadow(0 3px 6px rgba(0,0,0,0.35));">' +
                              '<path d="M20 0C8.95 0 0 8.95 0 20c0 14 20 32 20 32s20-18 20-32C40 8.95 31.05 0 20 0z" fill="#7f1d1d"/>' +
                              '<circle cx="20" cy="19" r="13" fill="white"/></svg>' +
                              '<i class="fas fa-briefcase" style="position:absolute; top:10px; left:50%; transform:translateX(-50%); color:#7f1d1d; font-size:15px;"></i></div>',
                        className: '', iconSize: [40, 52], iconAnchor: [20, 52]
                    });

                    var marker = null;
                    if (latInput.value && lngInput.value) {
                        marker = L.marker([lat, lng], { icon: iconoPin }).addTo(map);
                    }

                    function colocarPin(la, ln, z) {
                        latInput.value = parseFloat(la).toFixed(7);
                        lngInput.value = parseFloat(ln).toFixed(7);
                        if (marker) map.removeLayer(marker);
                        marker = L.marker([la, ln], { icon: iconoPin }).addTo(map);
                        map.setView([la, ln], z || 16);
                    }

                    map.on('click', function(e) { colocarPin(e.latlng.lat, e.latlng.lng, 16); });
                    latInput.addEventListener('change', function() {
                        var la = parseFloat(latInput.value), ln = parseFloat(lngInput.value);
                        if (la && ln) colocarPin(la, ln, 15);
                    });
                    lngInput.addEventListener('change', function() {
                        var la = parseFloat(latInput.value), ln = parseFloat(lngInput.value);
                        if (la && ln) colocarPin(la, ln, 15);
                    });

                    var inputDir = document.getElementById('buscar-direccion');
                    var btnBuscar = document.getElementById('btn-buscar-dir');
                    var resultsDiv = document.getElementById('resultados-direccion');

                    function buscarDireccion() {
                        var q = inputDir.value.trim();
                        if (q.length < 3) return;
                        btnBuscar.disabled = true;
                        btnBuscar.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Buscando...';

                        fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(q) + '&limit=5&addressdetails=1', {
                            headers: { 'Accept-Language': 'es' }
                        })
                        .then(function(r) { return r.json(); })
                        .then(function(data) {
                            btnBuscar.disabled = false;
                            btnBuscar.innerHTML = '<i class="fas fa-map-marker-alt mr-1"></i>Ubicar';
                            if (data.length === 0) {
                                resultsDiv.innerHTML = '<div style="padding:12px; text-align:center; color:#94a3b8; font-size:13px;">No se encontraron resultados</div>';
                                resultsDiv.style.display = 'block';
                                return;
                            }
                            if (data.length === 1) {
                                colocarPin(data[0].lat, data[0].lon, 17);
                                resultsDiv.style.display = 'none';
                                inputDir.value = data[0].display_name;
                                return;
                            }
                            var html = '';
                            data.forEach(function(item) {
                                html += '<button type="button" class="res-dir" style="display:flex;align-items:flex-start;gap:10px;width:100%;padding:10px 14px;border:none;background:none;cursor:pointer;text-align:left;border-bottom:1px solid #f1f5f9;" ' +
                                        'data-lat="' + item.lat + '" data-lng="' + item.lon + '" data-name="' + item.display_name.replace(/"/g, '&quot;') + '">' +
                                        '<i class="fas fa-map-marker-alt" style="color:#7f1d1d;margin-top:3px;"></i>' +
                                        '<span style="font-size:13px;color:#334155;">' + item.display_name + '</span></button>';
                            });
                            resultsDiv.innerHTML = html;
                            resultsDiv.style.display = 'block';
                            resultsDiv.querySelectorAll('.res-dir').forEach(function(btn) {
                                btn.addEventListener('click', function() {
                                    colocarPin(this.dataset.lat, this.dataset.lng, 17);
                                    inputDir.value = this.dataset.name;
                                    resultsDiv.style.display = 'none';
                                });
                            });
                        }).catch(function() {
                            btnBuscar.disabled = false;
                            btnBuscar.innerHTML = '<i class="fas fa-map-marker-alt mr-1"></i>Ubicar';
                        });
                    }

                    btnBuscar.addEventListener('click', buscarDireccion);
                    inputDir.addEventListener('keydown', function(e) { if (e.key === 'Enter') { e.preventDefault(); buscarDireccion(); } });
                    document.addEventListener('click', function(e) {
                        if (!inputDir.contains(e.target) && !resultsDiv.contains(e.target) && !btnBuscar.contains(e.target)) resultsDiv.style.display = 'none';
                    });
                });
                </script>

                {{-- Buttons --}}
                <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-slate_custom-200">
                    <a href="{{ route('condominio.companias.index') }}" class="btn-secondary">
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
