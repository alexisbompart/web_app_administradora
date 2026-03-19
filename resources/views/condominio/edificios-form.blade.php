<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-heading font-bold text-navy-800">
                    {{ isset($edificio) ? 'Editar Edificio' : 'Crear Edificio' }}
                </h2>
                <p class="text-sm text-slate_custom-400 mt-1">
                    {{ isset($edificio) ? 'Modificar datos del edificio' : 'Registrar nuevo edificio en el sistema' }}
                </p>
            </div>
            <a href="{{ route('condominio.edificios.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Volver
            </a>
        </div>
    </x-slot>

    <div class="card">
        <div class="card-header">
            <h3 class="text-sm font-heading font-semibold text-navy-800">
                <i class="fas fa-city mr-2 text-burgundy-800"></i>
                {{ isset($edificio) ? 'Formulario de Edicion' : 'Formulario de Registro' }}
            </h3>
        </div>
        <div class="card-body">
            <form action="{{ isset($edificio) ? route('condominio.edificios.update', $edificio) : route('condominio.edificios.store') }}" method="POST">
                @csrf
                @if(isset($edificio))
                    @method('PUT')
                @endif

                {{-- Basic Info --}}
                <h4 class="text-sm font-heading font-semibold text-navy-800 mb-4 pb-2 border-b border-slate_custom-200">
                    <i class="fas fa-info-circle mr-2 text-burgundy-800"></i>Informacion Basica
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <div>
                        <label for="cod_edif" class="block text-sm font-medium text-navy-800 mb-1">Codigo <span class="text-red-500">*</span></label>
                        <input type="text" name="cod_edif" id="cod_edif"
                               value="{{ old('cod_edif', $edificio->cod_edif ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               required>
                        @error('cod_edif')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="compania_id" class="block text-sm font-medium text-navy-800 mb-1">Compania <span class="text-red-500">*</span></label>
                        <select name="compania_id" id="compania_id"
                                class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                                required>
                            <option value="">Seleccione una compania</option>
                            @foreach($companias as $compania)
                                <option value="{{ $compania->id }}" {{ old('compania_id', $edificio->compania_id ?? '') == $compania->id ? 'selected' : '' }}>
                                    {{ $compania->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('compania_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nombre" class="block text-sm font-medium text-navy-800 mb-1">Nombre <span class="text-red-500">*</span></label>
                        <input type="text" name="nombre" id="nombre"
                               value="{{ old('nombre', $edificio->nombre ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               required>
                        @error('nombre')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="rif" class="block text-sm font-medium text-navy-800 mb-1">RIF</label>
                        <input type="text" name="rif" id="rif"
                               value="{{ old('rif', $edificio->rif ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               placeholder="J-12345678-9">
                        @error('rif')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="ciudad" class="block text-sm font-medium text-navy-800 mb-1">Ciudad</label>
                        <input type="text" name="ciudad" id="ciudad"
                               value="{{ old('ciudad', $edificio->ciudad ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                        @error('ciudad')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="telefono" class="block text-sm font-medium text-navy-800 mb-1">Telefono</label>
                        <input type="text" name="telefono" id="telefono"
                               value="{{ old('telefono', $edificio->telefono ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                        @error('telefono')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-navy-800 mb-1">Email</label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email', $edificio->email ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="total_aptos" class="block text-sm font-medium text-navy-800 mb-1">Total Apartamentos</label>
                        <input type="number" name="total_aptos" id="total_aptos"
                               value="{{ old('total_aptos', $edificio->total_aptos ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               min="0">
                        @error('total_aptos')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="lg:col-span-3">
                        <label for="direccion" class="block text-sm font-medium text-navy-800 mb-1">Direccion</label>
                        <input type="text" name="direccion" id="direccion"
                               value="{{ old('direccion', $edificio->direccion ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm">
                        @error('direccion')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Ubicacion / Coordenadas --}}
                <h4 class="text-sm font-heading font-semibold text-navy-800 mb-4 pb-2 border-b border-slate_custom-200">
                    <i class="fas fa-map-marker-alt mr-2 text-burgundy-800"></i>Ubicacion en el Mapa
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                    <div>
                        <label for="latitud" class="block text-sm font-medium text-navy-800 mb-1">Latitud</label>
                        <input type="number" name="latitud" id="latitud"
                               value="{{ old('latitud', $edificio->latitud ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               step="0.0000001" min="-90" max="90"
                               placeholder="Ej: 10.4806">
                        @error('latitud')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="longitud" class="block text-sm font-medium text-navy-800 mb-1">Longitud</label>
                        <input type="number" name="longitud" id="longitud"
                               value="{{ old('longitud', $edificio->longitud ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               step="0.0000001" min="-180" max="180"
                               placeholder="Ej: -66.9036">
                        @error('longitud')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-navy-800 mb-1">Buscar direccion</label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input type="text" id="buscar-direccion"
                                   placeholder="Escriba una direccion, ciudad o lugar..."
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
                <div class="mb-8">
                    <div class="rounded-xl overflow-hidden border border-slate_custom-200 shadow-sm" style="height: 350px;">
                        <div id="mapa-edificio" style="width: 100%; height: 100%;"></div>
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

                    var map = L.map('mapa-edificio').setView([lat, lng], zoom);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; OpenStreetMap',
                        maxZoom: 18
                    }).addTo(map);

                    var iconoPin = L.divIcon({
                        html: '<div style="position:relative; width:40px; height:52px;">' +
                              '<svg viewBox="0 0 40 52" width="40" height="52" style="filter: drop-shadow(0 3px 6px rgba(0,0,0,0.35));">' +
                              '<path d="M20 0C8.95 0 0 8.95 0 20c0 14 20 32 20 32s20-18 20-32C40 8.95 31.05 0 20 0z" fill="#7f1d1d"/>' +
                              '<circle cx="20" cy="19" r="13" fill="white"/>' +
                              '</svg>' +
                              '<i class="fas fa-building" style="position:absolute; top:10px; left:50%; transform:translateX(-50%); color:#7f1d1d; font-size:15px;"></i>' +
                              '</div>',
                        className: '',
                        iconSize: [40, 52],
                        iconAnchor: [20, 52]
                    });

                    var marker = null;
                    if (latInput.value && lngInput.value) {
                        marker = L.marker([lat, lng], { icon: iconoPin }).addTo(map);
                    }

                    map.on('click', function(e) {
                        latInput.value = e.latlng.lat.toFixed(7);
                        lngInput.value = e.latlng.lng.toFixed(7);
                        if (marker) map.removeLayer(marker);
                        marker = L.marker([e.latlng.lat, e.latlng.lng], { icon: iconoPin }).addTo(map);
                    });

                    latInput.addEventListener('change', actualizarMapa);
                    lngInput.addEventListener('change', actualizarMapa);

                    function actualizarMapa() {
                        var la = parseFloat(latInput.value);
                        var ln = parseFloat(lngInput.value);
                        if (la && ln) {
                            colocarPin(la, ln, 15);
                        }
                    }

                    function colocarPin(la, ln, z) {
                        latInput.value = parseFloat(la).toFixed(7);
                        lngInput.value = parseFloat(ln).toFixed(7);
                        if (marker) map.removeLayer(marker);
                        marker = L.marker([la, ln], { icon: iconoPin }).addTo(map);
                        map.setView([la, ln], z || 16);
                    }

                    // Geocoding con Nominatim (OpenStreetMap)
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
                                resultsDiv.innerHTML = '<div style="padding:12px; text-align:center; color:#94a3b8; font-size:13px;"><i class="fas fa-exclamation-circle mr-1"></i>No se encontraron resultados</div>';
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
                                html += '<button type="button" class="resultado-dir" style="display:flex; align-items:flex-start; gap:10px; width:100%; padding:10px 14px; border:none; background:none; cursor:pointer; text-align:left; border-bottom:1px solid #f1f5f9; font-family:inherit;" ' +
                                        'data-lat="' + item.lat + '" data-lng="' + item.lon + '" data-name="' + item.display_name.replace(/"/g, '&quot;') + '">' +
                                        '<i class="fas fa-map-marker-alt" style="color:#7f1d1d; margin-top:3px; flex-shrink:0;"></i>' +
                                        '<span style="font-size:13px; color:#334155; line-height:1.4;">' + item.display_name + '</span>' +
                                        '</button>';
                            });
                            resultsDiv.innerHTML = html;
                            resultsDiv.style.display = 'block';

                            resultsDiv.querySelectorAll('.resultado-dir').forEach(function(btn) {
                                btn.addEventListener('click', function() {
                                    colocarPin(this.dataset.lat, this.dataset.lng, 17);
                                    inputDir.value = this.dataset.name;
                                    resultsDiv.style.display = 'none';
                                });
                                btn.addEventListener('mouseover', function() { this.style.background = '#f8fafc'; });
                                btn.addEventListener('mouseout', function() { this.style.background = 'none'; });
                            });
                        })
                        .catch(function() {
                            btnBuscar.disabled = false;
                            btnBuscar.innerHTML = '<i class="fas fa-map-marker-alt mr-1"></i>Ubicar';
                        });
                    }

                    btnBuscar.addEventListener('click', buscarDireccion);
                    inputDir.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') {
                            e.preventDefault();
                            buscarDireccion();
                        }
                    });

                    document.addEventListener('click', function(e) {
                        if (!inputDir.contains(e.target) && !resultsDiv.contains(e.target) && !btnBuscar.contains(e.target)) {
                            resultsDiv.style.display = 'none';
                        }
                    });
                });
                </script>

                {{-- Financial Config --}}
                <h4 class="text-sm font-heading font-semibold text-navy-800 mb-4 pb-2 border-b border-slate_custom-200">
                    <i class="fas fa-calculator mr-2 text-burgundy-800"></i>Configuracion Financiera
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <div>
                        <label for="alicuota_base" class="block text-sm font-medium text-navy-800 mb-1">Alicuota Base</label>
                        <input type="number" name="alicuota_base" id="alicuota_base"
                               value="{{ old('alicuota_base', $edificio->alicuota_base ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               step="0.0001" min="0">
                        @error('alicuota_base')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="fondo_reserva_porcentaje" class="block text-sm font-medium text-navy-800 mb-1">Fondo Reserva (%)</label>
                        <input type="number" name="fondo_reserva_porcentaje" id="fondo_reserva_porcentaje"
                               value="{{ old('fondo_reserva_porcentaje', $edificio->fondo_reserva_porcentaje ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               step="0.01" min="0" max="100">
                        @error('fondo_reserva_porcentaje')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="mora_porcentaje" class="block text-sm font-medium text-navy-800 mb-1">Mora (%)</label>
                        <input type="number" name="mora_porcentaje" id="mora_porcentaje"
                               value="{{ old('mora_porcentaje', $edificio->mora_porcentaje ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               step="0.01" min="0" max="100">
                        @error('mora_porcentaje')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="dia_corte" class="block text-sm font-medium text-navy-800 mb-1">Dia de Corte</label>
                        <input type="number" name="dia_corte" id="dia_corte"
                               value="{{ old('dia_corte', $edificio->dia_corte ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               min="1" max="31">
                        @error('dia_corte')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="dia_vencimiento" class="block text-sm font-medium text-navy-800 mb-1">Dia de Vencimiento</label>
                        <input type="number" name="dia_vencimiento" id="dia_vencimiento"
                               value="{{ old('dia_vencimiento', $edificio->dia_vencimiento ?? '') }}"
                               class="w-full rounded-lg border-slate_custom-300 shadow-sm focus:border-burgundy-800 focus:ring-burgundy-800 text-sm"
                               min="1" max="31">
                        @error('dia_vencimiento')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center pt-6">
                        <input type="hidden" name="activo" value="0">
                        <input type="checkbox" name="activo" id="activo" value="1"
                               {{ old('activo', $edificio->activo ?? true) ? 'checked' : '' }}
                               class="rounded border-slate_custom-300 text-burgundy-800 shadow-sm focus:ring-burgundy-800">
                        <label for="activo" class="ml-2 text-sm font-medium text-navy-800">Activo</label>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate_custom-200">
                    <a href="{{ route('condominio.edificios.index') }}" class="btn-secondary">
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
